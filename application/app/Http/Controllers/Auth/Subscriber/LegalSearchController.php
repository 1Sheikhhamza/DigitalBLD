<?php

namespace App\Http\Controllers\Auth\Subscriber;

use App\Http\Controllers\Frontend\BaseController;
use App\Models\Bookmark;
use App\Models\DecisionComment;
use App\Models\DecisionShare;
use App\Models\Folder;
use App\Models\LegalDecisionUserNote;
use App\Models\OCRExtraction;
use App\Models\Subscriber;
use App\Models\UserFolderDecision;
use App\Models\Volume;
use App\Services\CommonService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Services\HomeService;
use Illuminate\Support\Facades\DB;

class LegalSearchController extends BaseController
{
    protected CommonService $commonService;
    protected $subscriberId;

    public function __construct(CommonService $commonService, HomeService $homeService)
    {
        parent::__construct($homeService);
        // $this->middleware('auth:subscriber');
        $this->commonService = $commonService;
        $this->middleware(function ($request, $next) {
            $this->subscriberId = auth('subscriber')->id();
            return $next($request);
        });
    }

    public function getActSuggestions(Request $request)
    {
        $term = $request->input('term');

        // Use Scout/Meilisearch for typo tolerance
        // We limit to 50 results for better suggestions
        $results = OCRExtraction::search($term)->paginate(50);

        // Scout returns a paginator of Models. We need to extract unique 'related_act_order_rule' strings.
        // Since we can't easily do "distinct" on the search engine side with simple Scout, we get more results and filter in PHP.
        // For larger properties, using raw() to get 'matches' might be better but paginate is simpler to start.

        $suggestions = $results->map(function ($item) {
            return $item->related_act_order_rule;
        })->filter()->unique()->values()->take(20);

        return response()->json($suggestions);
    }

    public function getPartiesSuggestions(Request $request)
    {
        $term = $request->input('term');

        // Use Scout/Meilisearch for typo tolerance
        // Meilisearch handles typos automatically (e.g. "omahmudl" will match "mahmudul")
        $results = OCRExtraction::search($term)->paginate(50);

        $suggestions = $results->map(function ($item) {
            return $item->parties;
        })->filter()->unique()->values()->take(20);

        return response()->json($suggestions);
    }

    public function leagalSearch(Request $request)
    {
        // If URL has `?new=1`, clear session
        if ($request->has('new') && $request->new == 1) {
            session()->forget('search_inputs');
        }

        // Get old inputs from session (if any)
        $inputs = session('search_inputs', []);
        $fillingYears = OCRExtraction::selectRaw("DISTINCT RIGHT(case_no, 4) as year")
            ->whereRaw("RIGHT(case_no, 4) REGEXP '^[0-9]{4}$'")
            ->whereBetween(DB::raw("RIGHT(case_no, 4)"), ['1900', '2099'])
            ->orderBy('year', 'ASC')
            ->pluck('year');

        $judgmentYear = OCRExtraction::selectRaw("DISTINCT REGEXP_SUBSTR(decided_on, '(19|20)[0-9]{2}') as year")
            ->whereRaw("decided_on REGEXP '(19|20)[0-9]{2}'")
            ->orderBy('year', 'ASC')
            ->pluck('year');


        $volumeList = $this->commonService->getVolume('BLD');
        $getJurisdiction = $this->commonService->getJurisdiction();
        return view('auth.subscribers.profile.legal_search', compact('inputs', 'fillingYears', 'judgmentYear', 'volumeList', 'getJurisdiction'));
    }


    public function handleSearch(Request $request)
    {
        session(['search_inputs' => $request->except('_token')]);
        return redirect()->route('subscriber.showResults');
    }

    public function showResults(Request $request)
    {

        $results = $this->searchResult($request);
        $inputs = session('search_inputs', []);
        if (isset($inputs['volume_number']) && $inputs['volume_number'] != '') {
            $inputs['volume_number'] = Volume::where('id', $inputs['volume_number'])->value('number');
        }
        $searchInputParams = $this->getInputParams($inputs);

        if (!$results) {
            return redirect()->route('auth.subscribers.profile.legal_search')->with('message', 'Please perform a search first.');
        }

        return view('auth.subscribers.profile.search_result', compact('results', 'searchInputParams'));
    }

    private function searchResult(Request $request)
    {
        $criteria = session('search_inputs', []);
        $query = OcrExtraction::query();

        // Keyword search (Scout + Meilisearch)
        if (!empty($criteria['searchKeyword'])) {
            $keyword = $criteria['searchKeyword'];

            // Initiate Scout Search
            // If other filters exist, we can use ->where() with Scout IF those fields are indexed as filterable in Meilisearch.
            // For now, simpler approach: Get IDs from Scout, then continue building Eloquent query.
            // This allows mixing Scout text search with complex SQL filters (like year ranges etc which might not be indexed yet).

            try {
                $keys = OCRExtraction::search($keyword)->keys();
                $query->whereIn('id', $keys);

                // Maintain relevance order from Scout?
                // If we use whereIn, order is lost in MySQL unless we use orderByRaw field(id, ids...).
                if ($keys->isNotEmpty()) {
                    $ids = $keys->implode(',');
                    $query->orderByRaw("FIELD(id, $ids)");
                }
            } catch (\Exception $e) {
                // Fallback to SQL search if Meilisearch is down
                $query->where(function ($q) use ($keyword) {
                    $q->where('judgment', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('key_words', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('parties', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('case_no', 'LIKE', '%' . $keyword . '%');
                });
            }
        }

        // Judgment Year filter (from decided_on)

        if (!empty($criteria['judgment_year'])) {
            $query->whereRaw("REGEXP_SUBSTR(decided_on, '(19|20)[0-9]{2}') = ?", [$criteria['judgment_year']]);
        }

        if (!empty($criteria['published_year'])) {
            $query->where('published_year', $criteria['published_year']);
        }

        if (!empty($criteria['section_subsection'])) {
            $query->where('sections_subsections', 'LIKE', '%' . $criteria['section_subsection'] . '%');
        }

        if (!empty($criteria['judges'])) {
            $query->where(function ($q) use ($criteria) {
                $q->where('judge_name', 'LIKE', '%' . $criteria['judges'] . '%');
            });
        }


        if (!empty($criteria['judgment_month'])) {
            $query->where('published_month', $criteria['judgment_month']);
        }

        if (!empty($criteria['keywords'])) {
            $query->where('key_words', 'LIKE', '%' . $criteria['keywords'] . '%');
        }

        if (!empty($criteria['act_rule_name'])) {
            $query->where('related_act_order_rule', 'LIKE', '%' . $criteria['act_rule_name'] . '%');
        }

        /* if (!empty($criteria['petitioners'])) {
            $query->where('petitioners', 'LIKE', '%' . $criteria['petitioners'] . '%');
        }

        if (!empty($criteria['respondent'])) {
            $query->where('respondent', 'LIKE', '%' . $criteria['respondent'] . '%');
        } */


        if (!empty($criteria['council'])) {
            $query->where(function ($q) use ($criteria) {
                $q->where('petitioners', 'LIKE', '%' . $criteria['council'] . '%')
                    ->orWhere('respondent', 'LIKE', '%' . $criteria['council'] . '%');
            });
        }

        if (!empty($criteria['parties'])) {
            $query->where('parties', 'LIKE', '%' . $criteria['parties'] . '%');
        }

        if (!empty($criteria['volume_number'])) {
            $query->where('volume_id', $criteria['volume_number']);
        }

        if (!empty($criteria['subject'])) {
            $query->where('subject', 'LIKE', '%' . $criteria['subject'] . '%');
        }

        if (!empty($criteria['case_number'])) {
            $query->where('case_no', 'LIKE', '%' . $criteria['case_number'] . '%');
        }

        if (!empty($criteria['jurisdiction'])) {
            $query->where('jurisdiction', $criteria['jurisdiction']);
        }

        if (!empty($criteria['page_number'])) {
            $page = (int) $criteria['page_number'];
            $query->where(function ($q) use ($page) {
                $q->where('starting_page_no', '<=', $page)
                    ->where('ending_page_no', '>=', $page);
            });
        }

        /* if (!empty($criteria['publication_year_range'])) {
            [$startPubYear, $endPubYear] = explode('-', $criteria['publication_year_range']);
            $query->whereBetween('published_year', [(int)$startPubYear, (int)$endPubYear]);
        } */

        // Paginate and keep query string
        return $query->paginate(30)->withQueryString();
    }

    private function getInputParams($criteria)
    {
        $filtered = array_filter($criteria, function ($value) {
            return !is_null($value) && $value !== '';
        });

        return $filtered;
        /* $formatted = [];
        foreach ($filtered as $key => $value) {
            $formatted[] = "$key: $value";
        }

        // Convert to comma-separated string
        $resultString = implode(', ', $formatted);

        // If you want JSON object
        // $resultJson = json_encode($filtered);
        return $resultString; */
    }

    public function singleDecision(Request $request, $id, $returnParam = null)
    {
        $returnParamString = $returnParam ? Crypt::decrypt($returnParam) : 'leagalSearch';
        $page = $request->query('page');

        $isBookmarked = Bookmark::where('user_id', auth('subscriber')->id())
            ->where('decision_id', $id)
            ->exists();

        $data = OCRExtraction::with('volume:id,number,year')->findOrFail($id);
        $allUsers = Subscriber::where('id', '!=', auth('subscriber')->id())->get();
        $decisionComment = DecisionComment::where('decision_id', $id)->get();
        $folders = Folder::where('user_id', auth('subscriber')->id())->get();

        $judgmentText = $data->judgment ?? '';

        // Get keywords for highlighting from session
        $inputs = session('search_inputs', []);
        $highlightKeywords = [];
        if (!empty($inputs['searchKeyword'])) {
            $highlightKeywords = explode(' ', $inputs['searchKeyword']);
        }

        $judgmentTextFormatted = $this->judgmentText($judgmentText, $highlightKeywords);

        // Default: sequential ID navigation
        $previousDecision = OCRExtraction::where('id', '<', $id)
            ->orderBy('id', 'desc')
            ->first();

        $nextDecision = OCRExtraction::where('id', '>', $id)
            ->orderBy('id', 'asc')
            ->first();

        // Custom: Volume Index navigation (Page based)
        // Check if we are coming from a volume index view
        if (strpos($returnParamString, 'volume.index/') !== false) {
            $currentStartPage = (int) $data->starting_page_no;

            // Previous: Same volume, Earlier page (or same page but lower ID)
            $previousDecision = OCRExtraction::where('volume_id', $data->volume_id)
                ->where('division', $data->division)
                ->where(function ($query) use ($currentStartPage, $id) {
                    $query->whereRaw('CAST(starting_page_no AS UNSIGNED) < ?', [$currentStartPage])
                        ->orWhere(function ($q) use ($currentStartPage, $id) {
                            $q->whereRaw('CAST(starting_page_no AS UNSIGNED) = ?', [$currentStartPage])
                                ->where('id', '<', $id);
                        });
                })
                ->orderByRaw('CAST(starting_page_no AS UNSIGNED) DESC')
                ->orderBy('id', 'DESC')
                ->first();

            // Next: Same volume, Later page (or same page but higher ID)
            $nextDecision = OCRExtraction::where('volume_id', $data->volume_id)
                ->where('division', $data->division)
                ->where(function ($query) use ($currentStartPage, $id) {
                    $query->whereRaw('CAST(starting_page_no AS UNSIGNED) > ?', [$currentStartPage])
                        ->orWhere(function ($q) use ($currentStartPage, $id) {
                            $q->whereRaw('CAST(starting_page_no AS UNSIGNED) = ?', [$currentStartPage])
                                ->where('id', '>', $id);
                        });
                })
                ->orderByRaw('CAST(starting_page_no AS UNSIGNED) ASC')
                ->orderBy('id', 'ASC')
                ->first();
        }


        $returnToVolume = $data->division == 'Appellate Division' ? 'legalDecisionAppellate' : 'legalDecisionHighCourt';
        $returnParamString = $returnParamString == $returnToVolume ? $returnParamString . '/' . $data->volume_id : $returnParamString;
        if ($page) {
            $returnParamString .= '?page=' . $page;
        }
        return view('auth.subscribers.profile.single_legal_decision', compact(
            'data',
            'judgmentTextFormatted',
            'previousDecision',
            'nextDecision',
            'isBookmarked',
            'decisionComment',
            'allUsers',
            'folders',
            'returnParamString'
        ));
    }


    public function myDecision($id)
    {
        $id = Crypt::decrypt($id);
        $myNotes = UserFolderDecision::join('legal_decision_user_notes', function ($join) {
            $join->on('user_folder_decisions.user_id', '=', 'legal_decision_user_notes.user_id')
                ->on('user_folder_decisions.decision_id', '=', 'legal_decision_user_notes.decision_id');
        })
            ->where('user_folder_decisions.user_id', auth('subscriber')->id())
            ->where('user_folder_decisions.id', $id)
            ->select('user_folder_decisions.*', 'legal_decision_user_notes.notes', 'legal_decision_user_notes.id AS noteId') // Select specific columns from join
            ->first();

        if (!$myNotes) {
            return redirect()->back()->with('error', 'No notes found.');
        }

        $data = OCRExtraction::with('volume:id,number,year')->find($myNotes->decision_id);
        if (!$data) {
            return redirect()->back()->with('error', 'Decision data not found.');
        }

        $checkSharedComment = DecisionShare::where('receiver_id', $this->subscriberId)
            ->where('decision_id', $myNotes->decision_id)
            ->exists();

        $getAllSharedUser = DecisionShare::with('receiver:id,name,email')
            ->where('sender_id', $this->subscriberId)
            ->where('decision_id', $myNotes->decision_id)
            ->get();

        $sharedUserIds = $getAllSharedUser->pluck('receiver.id')->toArray();

        $allUsers = Subscriber::where('id', '!=', auth('subscriber')->id())
            ->whereNotIn('id', $sharedUserIds)
            ->select('id', 'name', 'email')
            ->get();

        // $decisionComment = DecisionComment::where('decision_id', $myNotes->decision_id)->get();
        $decisionComment = DecisionComment::where('decision_id', $myNotes->decision_id)
            /* ->where(function ($query) use ($sharedUserIds) {
                $query->where('user_id', $this->subscriberId)
                    ->orWhereIn('user_id', $sharedUserIds);
            }) */
            ->where('request_id', $id)
            ->get();

        $judgmentText = $myNotes->notes ?? '';
        $judgmentTextFormatted = $this->judgmentText($judgmentText);
        $sharedDecision = false;

        $decisionFolderId = $id;
        return view('auth.subscribers.profile.my_legal_decision', compact(
            'data',
            'myNotes',
            'sharedDecision',
            'judgmentTextFormatted',
            'decisionComment',
            'allUsers',
            'getAllSharedUser',
            'decisionFolderId',
            'checkSharedComment'
        ));
    }



    public function sharedDecision($id)
    {
        $id = Crypt::decrypt($id);
        $sharedDecision = DecisionShare::where('receiver_id', $this->subscriberId)
            ->where('id', $id)
            ->first();

        $myNotes = UserFolderDecision::join('legal_decision_user_notes', function ($join) {
            $join->on('user_folder_decisions.user_id', '=', 'legal_decision_user_notes.user_id')
                ->on('user_folder_decisions.decision_id', '=', 'legal_decision_user_notes.decision_id');
        })
            ->where('user_folder_decisions.id', $sharedDecision->request_id)
            ->select('user_folder_decisions.*', 'legal_decision_user_notes.notes', 'legal_decision_user_notes.id AS noteId') // Select specific columns from join
            ->first();

        if (!$myNotes) {
            return redirect()->back()->with('error', 'No notes found.');
        }

        $data = OCRExtraction::find($myNotes->decision_id);
        if (!$data) {
            return redirect()->back()->with('error', 'Decision data not found.');
        }

        $checkSharedComment = DecisionShare::where('receiver_id', $this->subscriberId)
            ->where('decision_id', $myNotes->decision_id)
            ->exists();

        $getAllSharedUser = DecisionShare::with('receiver:id,name,email')
            ->where('sender_id', $this->subscriberId)
            ->where('decision_id', $myNotes->decision_id)
            ->get();

        $sharedUserIds = $getAllSharedUser->pluck('receiver.id')->toArray();

        $allUsers = Subscriber::where('id', '!=', auth('subscriber')->id())
            ->whereNotIn('id', $sharedUserIds)
            ->select('id', 'name', 'email')
            ->get();

        // $decisionComment = DecisionComment::where('decision_id', $myNotes->decision_id)->get();
        $decisionComment = DecisionComment::where('decision_id', $myNotes->decision_id)
            /* ->where(function ($query) use ($sharedUserIds) {
                $query->where('user_id', $this->subscriberId)
                    ->orWhereIn('user_id', $sharedUserIds);
            }) */
            ->where('request_id', $sharedDecision->request_id)
            ->get();

        $judgmentText = $myNotes->notes ?? '';
        $judgmentTextFormatted = $this->judgmentText($judgmentText);
        return view('auth.subscribers.profile.my_legal_decision', compact(
            'data',
            'sharedDecision',
            'judgmentTextFormatted',
            'decisionComment',
            'allUsers',
            'myNotes',
            'getAllSharedUser',
            'checkSharedComment'
        ));
    }


    public function downloadPdf($id)
    {
        $data = OCRExtraction::findOrFail($id);
        $userNote = LegalDecisionUserNote::where('user_id', auth('subscriber')->id())
            ->where('decision_id', $id)
            ->first();

        $judgmentText = $userNote && $userNote->notes
            ? $userNote->notes
            : ($data->judgment ?? '');
        $judgmentTextFormatted = $this->judgmentText($judgmentText);
        $metaData = true;

        $pdf = Pdf::loadView('auth.subscribers.profile.legal_decision_print', compact('data', 'metaData', 'userNote', 'judgmentTextFormatted'));
        return $pdf->download('legal-decision-' . $data->id . '.pdf');
    }

    public function printView($id, $type = null)
    {
        $data = OcrExtraction::findOrFail($id);
        $userNote = LegalDecisionUserNote::where('user_id', auth('subscriber')->id())
            ->where('decision_id', $id)
            ->first();

        $judgmentText = $userNote && $userNote->notes
            ? $userNote->notes
            : ($data->judgment ?? '');
        $judgmentTextFormatted = $this->judgmentText($judgmentText);

        if (isset($type) && $type == 'download') {
            $metaData = true;
        } else {
            $metaData = false;
        }
        return view('auth.subscribers.profile.legal_decision_print', compact('data', 'metaData', 'userNote', 'judgmentTextFormatted'));
    }


    private function judgmentText($text, $highlightKeywords = [])
    {

        // Normalize line breaks
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        // Break text into lines
        $lines = explode("\n", $text);

        $paragraph = '';
        $paragraphs = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') {
                // If empty line (rare), consider it a paragraph separator
                if (!empty($paragraph)) {
                    $paragraphs[] = $paragraph;
                    $paragraph = '';
                }
            } else {
                // Append line with a space
                $paragraph .= $trimmed . ' ';
            }
        }

        // Add last paragraph
        if (!empty($paragraph)) {
            $paragraphs[] = $paragraph;
        }

        // Stop words list
        $stopWords = ['the', 'of', 'and', 'a', 'an', 'in', 'is', 'was', 'that', 'it', 'to', 'for', 'on', 'are', 'as', 'with', 'at', 'by', 'this', 'from', 'or', 'be', 'no', 'not'];

        // Now wrap each paragraph
        $judgmentTextFormatted = '';
        foreach ($paragraphs as $para) {
            $content = trim($para);
            if (!empty($highlightKeywords)) {
                foreach ($highlightKeywords as $word) {
                    $cleanWord = trim($word);
                    // Skip if word is empty or is a stop word (case-insensitive check)
                    if ($cleanWord !== '' && !in_array(strtolower($cleanWord), $stopWords)) {
                        // Highlight keyword (case-insensitive)
                        // Use word boundary \b to prevent partial matches like 'the' in 'theme' (though stop words are skipped, this is good practice generally)
                        // But user wants partial if it's a significant word? Previous regex was partial "preg_quote($word)". 
                        // Keeping previous partial behavior for significant words but strictly filtering stop words.
                        // Actually, highlighting "act" in "action" might be desired or not. Let's stick to the previous simple partial match but filtered.
                        // However, highlighting "is" in "this" is bad. 
                        // Let's rely on the stop word filter primarily.

                        $content = preg_replace("/\b(" . preg_quote($cleanWord, '/') . ")\b/i", '<span style="background-color: yellow;">$1</span>', $content);
                    }
                }
            }
            $judgmentTextFormatted .= '<p>' . $content . '</p>';
        }

        return $judgmentTextFormatted;
    }


    public function bldVolume(Request $request)
    {
        $volumeList = $this->commonService->getVolume('BLD');
        $query = Volume::where('status', 1)->where('volume_type', 'BLD')->orderByRaw('CAST(number AS UNSIGNED) ASC');
        if (isset($request->volume)) {
            $query->where('id', $request->volume);
        }
        $volume_list = $query->paginate(24);
        return view('auth.subscribers.profile.bld_volume', compact('volume_list', 'volumeList'));
    }

    /* public function legalDecision($volume_id)
    {
        $volumeData = Volume::where('status', 1)
            ->where('id', $volume_id)
            ->firstOrFail();

        // Query for Appellate Division
        $appellateDecisions = OCRExtraction::whereNotNull('volume_id')
            ->where('division', 'Appellate Division')
            ->where('volume_id', $volume_id)
            ->orderBy('id', 'DESC')
            ->paginate(30, ['*'], 'appellate_page');

        // Query for High Court Division
        $highCourtDecisions = OCRExtraction::whereNotNull('volume_id')
            ->where('division', 'High Court Division')
            ->where('volume_id', $volume_id)
            ->orderBy('id', 'DESC')
            ->paginate(30, ['*'], 'highcourt_page');

        return view('auth.subscribers.profile.legal_decision_grid', compact('volumeData', 'appellateDecisions', 'highCourtDecisions'));
    } */


    public function legalDecisionIndex($volume_id)
    {
        $volumeData = Volume::where('status', 1)
            ->where('id', $volume_id)
            ->firstOrFail();

        $appellateDecisions = OCRExtraction::where('volume_id', $volume_id)
            ->where('division', 'Appellate Division')
            ->select('id', 'parties', 'starting_page_no', 'ending_page_no')
            ->orderBy('starting_page_no', 'asc') // Ordering by page number makes sense for an index
            ->get()
            ->map(function ($item) {
                $item->parties = $this->cleanPartyName($item->parties);
                return $item;
            });

        $highCourtDecisions = OCRExtraction::where('volume_id', $volume_id)
            ->where('division', 'High Court Division')
            ->select('id', 'parties', 'starting_page_no', 'ending_page_no')
            ->orderBy('starting_page_no', 'asc')
            ->get()
            ->map(function ($item) {
                $item->parties = $this->cleanPartyName($item->parties);
                return $item;
            });

        $allVolumes = Volume::where('status', 1)->where('volume_type', 'BLD')->orderBy('number', 'asc')->get(['id', 'number']);

        return view('auth.subscribers.profile.index', compact('volumeData', 'appellateDecisions', 'highCourtDecisions', 'allVolumes'));
    }

    private function cleanPartyName($name)
    {
        // 1. Remove HTML tags and decode entities
        $name = strip_tags(html_entity_decode($name, ENT_QUOTES | ENT_HTML5));
        // Double check for common stubbornly encoded entities
        $name = str_replace(['&amp;', '&nbsp;'], ['&', ' '], $name);

        // 2. Remove asterisks
        $name = str_replace('*', '', $name);

        // 3. Define status words (longest first)
        $statusWords = [
            'Plaintiff-Petitioner',
            'Defendant Appellants',
            'Plaintiff-Appellants',
            'Plaintiff-Respondents',
            'Defendant Opposite Part',
            'Condemned Petitioner',
            'Condemned Prisoners',
            'Condemned Prisoner',
            'Accused Petitioner',
            'Accused Applicant',
            'Opposite Parties',
            'Defendant-Appellant',
            'Defd-Appellant',
            'Decree-holder',
            'Objector',
            'Opposite Party',
            'Petitioners',
            'Petitioner',
            'Appellants',
            'Appellant',
            'Respondants',
            'Respondents',
            'Respondent',
            'Defendants',
            'Defendant',
            'Applicant',
            'Accused', // Added broadly as it often appears alone in parens like (Accused)
            'Plaintiff'
        ];

        // 4. Remove status words individually
        // Using \b to ensure we match whole words throughout the string
        foreach ($statusWords as $word) {
            $name = preg_replace('/\b' . preg_quote($word, '/') . '\b/iu', ' ', $name);
        }

        // Remove empty parentheses or parentheses containing only punctuation/spaces (often left over after status removal)
        // e.g. "( )", "(-)", "(. )"
        $name = preg_replace('/\([\s\.\-\_\x{2013}\x{2014}]*\)/u', ' ', $name);

        // 5. Clean up residual punctuation and specific garbage
        // Remove ellipsis
        $name = str_replace(["\xe2\x80\xa6", "\xE2\x80\xA6"], ' ', $name); // UTF-8 Bytes for … (U+2026)
        // Also regex match for unicode chars just in case
        $name = preg_replace('/\x{2026}/u', ' ', $name);

        // Remove 2+ sequences of dots, underscores, dashes (all types) allowing for spaces in between
        // e.g. "......" or ". . . . . ." or "_ _ _" or "- - -"
        $name = preg_replace('/(?:[\s]*[\._\-\x{2013}\x{2014}][\s]*){2,}/u', ' ', $name);

        // Remove stray hyphens or dots that might be left over (e.g. "Name - Name")
        // We want to keep hyphens IN names (Al-Helal), but not floating ones.
        // Floating hyphen: space hyphen space
        $name = preg_replace('/\s+[\-\x{2013}\x{2014}]\s+/u', ' ', $name);

        // Remove leading/trailing punctuation (dots, hyphens, underscores, unicode dashes)
        // "Name." -> "Name"
        // ".Name" -> "Name"
        $name = preg_replace('/^[\.\-\s_\x{2013}\x{2014}]+|[\.\-\s_\x{2013}\x{2014}]+$/u', '', $name);

        // 6. Final whitespace normalization
        return trim(preg_replace('/\s+/', ' ', $name));
    }

    public function legalDecisionAppellate($volume_id)
    {
        $volumeData = Volume::where('status', 1)
            ->where('id', $volume_id)
            ->firstOrFail();

        $appellateDecisions = OCRExtraction::whereNotNull('volume_id')
            ->where('division', 'Appellate Division')
            ->where('volume_id', $volume_id)
            ->orderByRaw('CAST(starting_page_no AS UNSIGNED) ASC')
            ->orderBy('id', 'ASC')
            ->paginate(30);

        $allVolumes = Volume::where('status', 1)->where('volume_type', 'BLD')->orderBy('number', 'asc')->get(['id', 'number']);

        return view('auth.subscribers.profile.appellate', compact('volumeData', 'appellateDecisions', 'allVolumes'));
    }

    public function legalDecisionHighCourt($volume_id)
    {
        $volumeData = Volume::where('status', 1)
            ->where('id', $volume_id)
            ->firstOrFail();

        $highCourtDecisions = OCRExtraction::whereNotNull('volume_id')
            ->where('division', 'High Court Division')
            ->where('volume_id', $volume_id)
            ->orderByRaw('CAST(starting_page_no AS UNSIGNED) ASC')
            ->orderBy('id', 'ASC')
            ->paginate(30);

        $allVolumes = Volume::where('status', 1)->where('volume_type', 'BLD')->orderBy('number', 'asc')->get(['id', 'number']);

        return view('auth.subscribers.profile.highcourt', compact('volumeData', 'highCourtDecisions', 'allVolumes'));
    }

    // SCOB Volume Methods (Year-based from published_year or case_no regex)
    public function scobVolume(Request $request)
    {
        // Extract filing year using Regex: finds /YYYY or "of YYYY" pattern
        // Or use published_year column if available
        $yearList = OCRExtraction::join('volumes', 'ocr_extractions.volume_id', '=', 'volumes.id')
            ->where('volumes.volume_type', 'SCOB')
            ->where('volumes.status', 1)
            ->where('division', 'LIKE', 'SCOB%')
            ->selectRaw("volumes.year as year, COUNT(*) as count")
            ->groupBy('volumes.year')
            ->orderBy('volumes.year', 'desc')
            ->get();

        return view('auth.subscribers.profile.scob_volume', compact('yearList'));
    }

    public function scobYearIndex($year)
    {
        // Logic: Use published_year = $year OR regex matches year
        $appellateDecisions = OCRExtraction::join('volumes', 'ocr_extractions.volume_id', '=', 'volumes.id')
            ->where('volumes.volume_type', 'SCOB')
            ->where('volumes.status', 1)
            ->where('volumes.year', $year)
            ->where('division', 'LIKE', 'SCOB%')
            ->where(function ($q) {
                $q->where('division', 'LIKE', '%Appellate%')
                    ->orWhere('division', '=', 'SCOB');
            })
            ->select('ocr_extractions.id', 'case_no', 'parties', 'decided_on')
            ->orderBy('case_no', 'desc')
            ->get();

        $highCourtDecisions = OCRExtraction::join('volumes', 'ocr_extractions.volume_id', '=', 'volumes.id')
            ->where('volumes.volume_type', 'SCOB')
            ->where('volumes.status', 1)
            ->where('volumes.year', $year)
            ->where('division', 'LIKE', 'SCOB%')
            ->where('division', 'LIKE', '%High Court%')
            ->select('ocr_extractions.id', 'case_no', 'parties', 'decided_on')
            ->orderBy('case_no', 'desc')
            ->get();

        $allYears = Volume::where('volume_type', 'SCOB')
            ->where('status', 1)
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('auth.subscribers.profile.scob_year_index', compact('year', 'appellateDecisions', 'highCourtDecisions', 'allYears'));
    }

    public function scobYearAppellate($year)
    {
        $appellateDecisions = OCRExtraction::where('division', 'LIKE', 'SCOB%')
            ->whereRaw('SUBSTRING(REGEXP_SUBSTR(case_no, "/[0-9]{4}"), 2) = ?', [$year])
            ->where(function ($q) {
                $q->where('division', 'LIKE', '%Appellate%')->orWhere('division', '=', 'SCOB');
            })
            ->select('id', 'case_no', 'parties', 'decided_on', 'judgment')
            ->orderBy('case_no', 'desc')
            ->paginate(30);

        $allYears = OCRExtraction::where('division', 'LIKE', 'SCOB%')
            ->whereNotNull('case_no')
            ->selectRaw('DISTINCT SUBSTRING(REGEXP_SUBSTR(case_no, "/[0-9]{4}"), 2) as year')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->filter(function ($y) {
                return is_numeric($y) && $y >= 1900 && $y <= 2099;
            });

        return view('auth.subscribers.profile.scob_year_appellate', compact('year', 'appellateDecisions', 'allYears'));
    }

    public function scobYearHighCourt($year)
    {
        $highCourtDecisions = OCRExtraction::where('division', 'LIKE', 'SCOB%')
            ->whereRaw('SUBSTRING(REGEXP_SUBSTR(case_no, "/[0-9]{4}"), 2) = ?', [$year])
            ->where('division', 'LIKE', '%High Court%')
            ->select('id', 'case_no', 'parties', 'decided_on', 'judgment')
            ->orderBy('case_no', 'desc')
            ->paginate(30);

        $allYears = OCRExtraction::where('division', 'SCOB')
            ->whereNotNull('case_no')
            ->selectRaw('DISTINCT SUBSTRING(REGEXP_SUBSTR(case_no, "/[0-9]{4}"), 2) as year')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->filter(function ($y) {
                return is_numeric($y) && $y >= 1900 && $y <= 2099;
            });

        return view('auth.subscribers.profile.scob_year_highcourt', compact('year', 'highCourtDecisions', 'allYears'));
    }
}
