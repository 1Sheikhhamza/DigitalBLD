<?php

namespace App\Http\Controllers\Auth\Subscriber;

use App\Http\Controllers\Frontend\BaseController;
use App\Services\HomeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use DOMDocument;
use DOMXPath;

class SupremeSearchController extends BaseController
{
    public function __construct(HomeService $homeService)
    {
        parent::__construct($homeService);
    }

    public function index()
    {
        return view('auth.subscribers.profile.supreme_search');
    }

    public function results(Request $request)
    {
        // Store search parameters in session for "Modify Search"
        session(['supreme_search_params' => $request->all()]);

        return view('auth.subscribers.profile.supreme_search_results', [
            'searchParams' => $request->all()
        ]);
    }

    public function viewPdf(Request $request)
    {
        $pdfUrl = $request->query('url');
        $caseTitle = $request->query('title', 'Judgment Document');
        $result = $request->query('result', '');

        if (!$pdfUrl) {
            return redirect()->route('subscriber.supremeSearch')->with('error', 'Invalid PDF URL');
        }

        // Use the proxy URL
        $proxyUrl = route('subscriber.supremeSearch.proxyPdf') . '?url=' . urlencode($pdfUrl);

        return view('auth.subscribers.profile.supreme_search_pdf', [
            'pdfUrl' => $proxyUrl,
            'caseTitle' => $caseTitle,
            'result' => $result
        ]);
    }

    public function proxy(Request $request)
    {
        try {
            $baseUrl = 'https://www.supremecourt.gov.bd/web/index.php';

            // --- Smart Search Input Parsing ---
            $rawCaseNumber = trim($request->input('case_number', ''));
            // Regex to match "Type Number/Year" e.g., "Civil Revision 1542/2009"
            // Flexible matching: (Type) space (Number) / (Year)
            if (!empty($rawCaseNumber) && preg_match('/^([a-zA-Z\s]+)\s+(\d+)\/(\d{4})$/i', $rawCaseNumber, $matches)) {
                $typeStr = trim($matches[1]);
                $numberStr = $matches[2];
                $yearStr = $matches[3];

                $typeId = $this->getCaseTypeIdByName($typeStr);

                if ($typeId) {
                    $request->merge([
                        'case_type_id' => $typeId,
                        'case_number' => $numberStr,
                        'year' => $yearStr
                    ]);
                    \Illuminate\Support\Facades\Log::info('Smart Search Parsed', ['raw' => $rawCaseNumber, 'type' => $typeStr, 'id' => $typeId, 'no' => $numberStr, 'year' => $yearStr]);
                }
            }
            // ----------------------------------

            // Build query parameters
            // Build query parameters
            $divId = $request->input('div_id', '2');
            $currPage = (int) $request->input('page', 1);
            $start = ($currPage - 1) * 50;

            $params = array_merge([
                'page' => 'judgments.php',
                'div_id' => $divId,
                'menu' => '00',
                'start' => $start, // Use start parameter for pagination
            ], $request->only(['case_type_id', 'case_number', 'year', 'parties', 'description']));

            \Illuminate\Support\Facades\Log::info('Supreme Search Request', ['url' => $baseUrl, 'params' => $params]);

            // Forward request to Supreme Court website
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Referer' => 'https://www.supremecourt.gov.bd/web/',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            ])->withoutVerifying()->get($baseUrl, $params);

            \Illuminate\Support\Facades\Log::info('Supreme Search Response Status', ['status' => $response->status(), 'length' => strlen($response->body())]);

            // Parse HTML and extract judgment data
            $judgments = $this->parseJudgments($response->body());

            // --- Custom Sorting Logic for "Exact Match" ---
            $searchCaseNumber = trim($request->input('case_number', ''));
            $searchYear = trim($request->input('year', ''));

            if (!empty($searchCaseNumber) || !empty($searchYear)) {
                usort($judgments, function ($a, $b) use ($searchCaseNumber, $searchYear) {
                    $scoreA = 0;
                    $scoreB = 0;

                    $titleA = $a['case_title'];
                    $titleB = $b['case_title'];

                    // --- Scoring for A ---
                    if (!empty($searchCaseNumber)) {
                        // Exact match of case number as a standalone word/number in the title
                        // Using word boundaries \b to avoid matching "64" inside "164"
                        if (preg_match('/(?<!\d)' . preg_quote($searchCaseNumber, '/') . '(?!\d)/', $titleA)) {
                            $scoreA += 50;
                        }
                    }
                    if (!empty($searchYear)) {
                        // Exact match of year
                        if (strpos($titleA, $searchYear) !== false) {
                            $scoreA += 30;
                        }
                    }
                    // Boost if BOTH match (High confidence "Perfect Match")
                    if (!empty($searchCaseNumber) && !empty($searchYear) && $scoreA >= 80) {
                        $scoreA += 20; // Bonus
                    }


                    // --- Scoring for B ---
                    if (!empty($searchCaseNumber)) {
                        if (preg_match('/(?<!\d)' . preg_quote($searchCaseNumber, '/') . '(?!\d)/', $titleB)) {
                            $scoreB += 50;
                        }
                    }
                    if (!empty($searchYear)) {
                        if (strpos($titleB, $searchYear) !== false) {
                            $scoreB += 30;
                        }
                    }
                    if (!empty($searchCaseNumber) && !empty($searchYear) && $scoreB >= 80) {
                        $scoreB += 20;
                    }

                    // Sort Descending by Score
                    if ($scoreA == $scoreB) {
                        return 0;
                    }
                    return ($scoreA > $scoreB) ? -1 : 1;
                });
            }
            // ----------------------------------------------

            \Illuminate\Support\Facades\Log::info('Supreme Search Parsed Count', ['count' => count($judgments)]);

            return response()->json([
                'success' => true,
                'data' => $judgments,
                'count' => count($judgments)
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Supreme Search Error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'error' => 'Error fetching data: ' . $e->getMessage()
            ], 500);
        }
    }

    private function parseJudgments($html)
    {
        $judgments = [];

        // Suppress warnings for malformed HTML
        libxml_use_internal_errors(true);

        $dom = new DOMDocument();
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new DOMXPath($dom);

        // Find all table rows (skip header row)
        $rows = $xpath->query("//table//tr[position() > 1]");

        foreach ($rows as $row) {
            // Get all cells in the row
            $cells = $xpath->query('.//td', $row);

            if ($cells->length < 3) {
                continue; // Skip rows without enough columns
            }

            // Column 1: SL (skip, not needed)
            // Column 2: Case Number with PDF link, upload date, and court
            // Column 3: Parties
            // Column 4: Short Description (Result)

            $caseNumberCell = $cells->item(1); // Second column
            $partiesCell = $cells->item(2);    // Third column
            $resultCell = $cells->item(3);     // Fourth column (if exists)

            if (!$caseNumberCell) {
                continue;
            }

            // Extract PDF link and case title from Case Number column
            $pdfLink = $xpath->query('.//a[contains(@href, ".pdf") and not(contains(text(), "অনুবাদ")) and not(contains(text(), "Google"))]', $caseNumberCell)->item(0);

            if (!$pdfLink) {
                continue;
            }

            $pdfUrl = $pdfLink->getAttribute('href');
            $caseTitle = trim($pdfLink->textContent);

            // Clean up duplicate case numbers in brackets
            // e.g., "Writ Petition 10510/2018(Writ Petition 10510/2018)" -> "Writ Petition 10510/2018"
            $caseTitle = preg_replace('/\(([^)]+)\)$/', '', $caseTitle);
            $caseTitle = trim($caseTitle);

            // Extract upload date from Case Number column
            $uploadDate = 'N/A';
            $caseNumberText = $caseNumberCell->textContent;
            if (preg_match('/Uploaded\s+on\s*:\s*([0-9]{2}-[A-Z]{3}-[0-9]{2})/i', $caseNumberText, $dateMatch)) {
                $uploadDate = $dateMatch[1];
            }

            // Extract court/location from Case Number column (From : ...)
            $court = 'N/A';
            if (preg_match('/From\s*:\s*([^\n]+)/i', $caseNumberText, $courtMatch)) {
                $court = trim($courtMatch[1]);
                // Clean up any extra whitespace or translation text
                $court = preg_replace('/অনুবাদ.*$/u', '', $court);
                $court = trim($court);
            }

            // Extract parties from Parties column
            $parties = 'N/A';
            if ($partiesCell) {
                $parties = trim($partiesCell->textContent);
                // Remove any translation text
                $parties = preg_replace('/অনুবাদ.*$/u', '', $parties);
                $parties = preg_replace('/\s+/', ' ', trim($parties));

                // Skip this row if parties contains navigation/menu keywords
                $navigationKeywords = [
                    'Judgment : High Court Division',
                    'Full List',
                    'Case Type',
                    'Letter of Administration',
                    'SuitMisc',
                    'Violation',
                    'MiscReview',
                    'Death Reference',
                    'Execution',
                    'Contempt Matter',
                    'Customs Appeal',
                    'Trademark Application',
                    'Civil Rule',
                    'Criminal Rule',
                    'Select -'
                ];

                foreach ($navigationKeywords as $keyword) {
                    if (stripos($parties, $keyword) !== false) {
                        continue 2; // Skip this entire row
                    }
                }
            }

            // Extract result/short description from Short Description column
            $result = 'N/A';
            if ($resultCell) {
                $result = trim($resultCell->textContent);
                $result = preg_replace('/\s+/', ' ', trim($result));
            }

            // Skip if we couldn't extract meaningful data
            if (empty($caseTitle) || strlen($caseTitle) < 5) {
                continue;
            }

            // Additional validation: skip if parties is too long (likely contains junk)
            if (strlen($parties) > 500) {
                continue;
            }

            $judgment = [
                'pdf_url' => $this->normalizeUrl($pdfUrl),
                'case_title' => $caseTitle,
                'parties' => $parties,
                'upload_date' => $uploadDate,
                'result' => $result,
            ];

            $judgments[] = $judgment;
        }

        libxml_clear_errors();

        return $judgments;
    }

    private function normalizeUrl($url)
    {
        // Convert relative URLs to absolute
        if (strpos($url, 'http') !== 0) {
            $baseUrl = 'https://www.supremecourt.gov.bd/web/';
            $url = rtrim($baseUrl, '/') . '/' . ltrim($url, '/');
        }
        return $url;
    }

    public function proxyPdf(Request $request)
    {
        $url = $request->query('url');

        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            return response('Invalid URL', 400);
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Referer' => 'https://www.supremecourt.gov.bd/web/',
            ])->withoutVerifying()->timeout(60)->get($url);

            return response($response->body())
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="judgment.pdf"')
                ->header('Cache-Control', 'public, max-age=3600');
        } catch (\Exception $e) {
            return response('Error fetching PDF: ' . $e->getMessage(), 500);
        }
    }

    public function getCaseTypes(Request $request)
    {
        try {
            $divId = $request->input('div_id', '2');
            $url = 'https://www.supremecourt.gov.bd/web/index.php?page=judgments.php&div_id=' . $divId . '&menu=00';

            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Referer' => 'https://www.supremecourt.gov.bd/web/',
            ])->withoutVerifying()->get($url);

            // Parse HTML to extract case types from select dropdown
            libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            $dom->loadHTML(mb_convert_encoding($response->body(), 'HTML-ENTITIES', 'UTF-8'));
            $xpath = new DOMXPath($dom);

            // Find the case type select element (usually named 'case_type_id' or similar)
            $options = $xpath->query("//select[@name='case_type_id']/option | //select[@id='case_type_id']/option | //select[contains(@name, 'case')]/option");

            $caseTypes = [];

            foreach ($options as $option) {
                $value = trim($option->getAttribute('value'));
                $text = trim($option->textContent);

                // Skip empty options
                if ($value && $text && $value !== '0' && $text !== 'Select' && $text !== 'All') {
                    $caseTypes[] = [
                        'id' => $value,
                        'name' => $text
                    ];
                }
            }

            libxml_clear_errors();

            // If no case types found from form, return default list
            if (empty($caseTypes)) {
                $caseTypes = [
                    ['id' => '1', 'name' => 'Civil Appeal'],
                    ['id' => '2', 'name' => 'Civil Petition'],
                    ['id' => '3', 'name' => 'Civil Revision'],
                    ['id' => '4', 'name' => 'Civil Review Petition'],
                    ['id' => '5', 'name' => 'Criminal Appeal'],
                    ['id' => '6', 'name' => 'Criminal Petition'],
                    ['id' => '7', 'name' => 'Criminal Revision'],
                    ['id' => '8', 'name' => 'Criminal Review Petition'],
                    ['id' => '9', 'name' => 'Death Reference'],
                    ['id' => '10', 'name' => 'Jail Appeal'],
                    ['id' => '11', 'name' => 'Jail Petition'],
                    ['id' => '12', 'name' => 'Miscellaneous Case'],
                    ['id' => '13', 'name' => 'Writ Petition'],
                    ['id' => '14', 'name' => 'Company Matter'],
                    ['id' => '15', 'name' => 'Contempt Petition'],
                    ['id' => '16', 'name' => 'Reference'],
                    ['id' => '17', 'name' => 'Special Case'],
                    ['id' => '18', 'name' => 'Criminal Misc (TN)'],
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $caseTypes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error fetching case types: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getCaseTypeIdByName($name)
    {
        $map = [
            'civilandcriminalreviewpetition' => '',
            'civilappeal' => '35', // Was 1/3, actually 35 in HCD
            'civilpetition' => '36',
            'civilrevision' => '6', // Was 3, actually 6 in HCD
            'civilreviewpetition' => '38',
            'criminalappeal' => '9', // Criminal Appeal(H) is 9
            'criminalpetition' => '40',
            'criminalrevision' => '10',
            'criminalreviewpetition' => '42',
            'deathreference' => '46',
            'jailappeal' => '12', // Jail Appeal(H) is 12
            'jailpetition' => '43',
            'miscellaneouscase' => '12', // Check this!
            'writpetition' => '13',
            'companymatter' => '22',
            'contemptpetition' => '69',
            'reference' => '16', // Suo-Muto Rule
            'specialcase' => '17', // Transfer Petition?
            'criminalmisctn' => '18', // Admiralty Suit?? No, map needs verification
            'criminalmisc' => '11',
            'firstappeal' => '1',
            'firstmiscappeal' => '2',
            'civilarule' => '5',
            'civilmisc' => '7',
            'vatappeal' => '30',
        ];

        $cleanName = strtolower(preg_replace('/[^a-zA-Z]/', '', $name));

        return $map[$cleanName] ?? null;
    }
}

