<?php

namespace App\Services;

use App\Repositories\Contracts\OCRExtractionRepositoryInterface;
use Google\Cloud\Vision\V1\Image;
use Google\Cloud\Vision\V1\Feature;
use Google\Cloud\Vision\V1\AnnotateImageRequest;
use Google\Cloud\Vision\V1\BatchAnnotateImagesRequest;
// use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Client\ImageAnnotatorClient;
use PhpOffice\PhpWord\IOFactory;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Str;

class OCRExtractionService
{
    protected $repository;
    protected $googleClient;

    public function __construct(OCRExtractionRepositoryInterface $repository)
    {
        $this->repository = $repository;

        // Initialize client once to reuse
        $this->googleClient = new ImageAnnotatorClient([
            'credentials' => storage_path('app/google/ocr-text-extraction-462805-2de2e6f070ec.json'),
        ]);
    }

    public function index()
    {
        return $this->repository->index();
    }

    public function create($data)
    {
        return $this->repository->create($data);
    }

    public function extractText($filePath, $extension)
    {
        switch (strtolower($extension)) {
            case 'jpg':
            case 'jpeg':
            case 'png':
                return $this->extractTextFromImage($filePath);

            case 'doc':
            case 'docx':
                return $this->extractTextFromWord($filePath);

            case 'pdf':
                return $this->extractTextFromPdf($filePath);

            default:
                return '';
        }
    }

    /* private function extractTextFromImage(string $filePath): string
    {
        $content = file_get_contents($filePath);
        $image = (new Image())->setContent($content);
        $feature = (new Feature())->setType(Feature\Type::DOCUMENT_TEXT_DETECTION);

        $request = (new AnnotateImageRequest())
            ->setImage($image)
            ->setFeatures([$feature]);

        $batchRequest = (new BatchAnnotateImagesRequest())
            ->setRequests([$request]);

        $response = $this->googleClient->batchAnnotateImages($batchRequest);

        $text = '';
        foreach ($response->getResponses() as $res) {
            if ($res->hasError()) {
                throw new \Exception($res->getError()->getMessage());
            }
            $annotation = $res->getFullTextAnnotation();
            $text .= $annotation ? $annotation->getText() : '';
        }

        return $text;
    } */


    /* private function extractTextFromImage(string $filePath): string
    {
        $content = file_get_contents($filePath);
        $image = (new Image())->setContent($content);
        $feature = (new Feature())->setType(Feature\Type::DOCUMENT_TEXT_DETECTION);

        $request = (new AnnotateImageRequest())
            ->setImage($image)
            ->setFeatures([$feature]);

        $batchRequest = (new BatchAnnotateImagesRequest())
            ->setRequests([$request]);

        $response = $this->googleClient->batchAnnotateImages($batchRequest);

        $fullText = '';

        foreach ($response->getResponses() as $res) {
            if ($res->hasError()) {
                throw new \Exception($res->getError()->getMessage());
            }

            $annotation = $res->getFullTextAnnotation();
            if (!$annotation) {
                continue;
            }

            // Iterate pages
            foreach ($annotation->getPages() as $page) {
                // Iterate blocks
                foreach ($page->getBlocks() as $block) {
                    // Optional: You can check block type if you want to filter, e.g., TEXT block
                    // Iterate paragraphs in block
                    foreach ($block->getParagraphs() as $paragraph) {
                        $paragraphText = '';
                        // Iterate words in paragraph
                        foreach ($paragraph->getWords() as $word) {
                            $wordText = '';
                            // Iterate symbols in word
                            foreach ($word->getSymbols() as $symbol) {
                                $wordText .= $symbol->getText();
                            }
                            // Append word + space
                            $paragraphText .= $wordText . ' ';
                        }
                        // Append paragraph text with newline to preserve paragraph breaks
                        $fullText .= trim($paragraphText) . "\n";
                    }
                    // Optionally add extra newline between blocks for spacing
                    $fullText .= "\n";
                }
            }
        }

        return trim($fullText);
    } */


    private function extractTextFromImage(string $filePath): string
    {
        $content = file_get_contents($filePath);
        $image = (new Image())->setContent($content);
        $feature = (new Feature())->setType(Feature\Type::DOCUMENT_TEXT_DETECTION);

        $request = (new AnnotateImageRequest())
            ->setImage($image)
            ->setFeatures([$feature]);

        $batchRequest = (new BatchAnnotateImagesRequest())
            ->setRequests([$request]);

        $response = $this->googleClient->batchAnnotateImages($batchRequest);

        $leftColumnBlocks = [];
        $rightColumnBlocks = [];

        foreach ($response->getResponses() as $res) {
            if ($res->hasError()) {
                throw new \Exception($res->getError()->getMessage());
            }

            $annotation = $res->getFullTextAnnotation();
            if (!$annotation) {
                continue;
            }

            foreach ($annotation->getPages() as $page) {
                foreach ($page->getBlocks() as $block) {
                    $vertices = $block->getBoundingBox()->getVertices();

                    $x = $vertices[0]->getX(); // Top-left X

                    // Heuristic: everything left of page center is left column, right is right column
                    $midpoint = $page->getWidth() / 2;

                    $paragraphText = '';
                    foreach ($block->getParagraphs() as $paragraph) {
                        foreach ($paragraph->getWords() as $word) {
                            foreach ($word->getSymbols() as $symbol) {
                                $paragraphText .= $symbol->getText();
                            }
                            $paragraphText .= ' ';
                        }
                        $paragraphText .= "\n";
                    }

                    $entry = [
                        'y' => $vertices[0]->getY(),
                        'text' => trim($paragraphText),
                    ];

                    if ($x < $midpoint) {
                        $leftColumnBlocks[] = $entry;
                    } else {
                        $rightColumnBlocks[] = $entry;
                    }
                }
            }
        }

        // Sort top-to-bottom by Y coordinate
        usort($leftColumnBlocks, fn($a, $b) => $a['y'] <=> $b['y']);
        usort($rightColumnBlocks, fn($a, $b) => $a['y'] <=> $b['y']);

        $leftText = implode("\n", array_column($leftColumnBlocks, 'text'));
        $rightText = implode("\n", array_column($rightColumnBlocks, 'text'));

        return trim($leftText . "\n\n" . $rightText);
    }



    private function extractTextFromWord($filePath)
    {
        $phpWord = IOFactory::load($filePath);
        $text = '';

        foreach ($phpWord->getSections() as $section) {
            $elements = $section->getElements();
            foreach ($elements as $element) {
                if (method_exists($element, 'getText')) {
                    $text .= $element->getText() . " ";
                }
            }
        }

        return trim($text);
    }

    private function extractTextFromPdf($path)
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($path);
        $text = trim($pdf->getText());

        if (!empty($text)) {
            return $text; // PDF has selectable text
        }

        // Otherwise, scanned PDF – use OCR
        return $this->extractTextFromPdfUsingOcr($path);
    }

    private function extractTextFromPdfUsingOcr($pdfPath)
    {
        $imagick = new \Imagick();
        $imagick->setResolution(300, 300); // High DPI for OCR
        $imagick->readImage($pdfPath);

        $text = '';
        $uniqueId = Str::uuid();
        $outputDir = storage_path("app/ocr_pages/{$uniqueId}");
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        $pageIndex = 1;
        foreach ($imagick as $page) {
            $page->setImageFormat('jpeg');
            $imagePath = "{$outputDir}/page_{$pageIndex}.jpg";
            $page->writeImage($imagePath);

            $text .= $this->extractTextFromImage($imagePath) . "\n";
            $pageIndex++;
        }

        $imagick->clear();
        $imagick->destroy();

        return trim($text);
    }


    public function splitLegalText(string $text, array $headings): array
    {
        $endKeywords = ['SMZH', 'S.M.Z.H', 'KAK', 'Ed.', 'End.'];

        $pattern = '/(' . implode('|', array_map('preg_quote', $headings)) . ')/';
        // Split text into parts based on headings, keeping the headings
        $parts = preg_split($pattern, $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        $result = [];
        for ($i = 0; $i < count($parts); $i++) {
            $part = trim($parts[$i]);
            if (in_array($part, $headings)) {
                $title = $part;
                $body = $parts[$i + 1] ?? '';
                // Split the body by lines
                $lines = preg_split('/\r?\n/', $body);
                $sectionContentLines = [];

                foreach ($lines as $line) {
                    $sectionContentLines[] = $line;

                    // Check if line contains any of the end keywords to stop section here
                    foreach ($endKeywords as $endKeyword) {
                        if (preg_match('/\b' . preg_quote($endKeyword, '/') . '\b/i', $line)) {
                            // Stop appending lines when end keyword found
                            break 2;
                        }
                    }
                }

                $content = trim(implode("\n", $sectionContentLines));

                $result[] = [
                    'title' => $title,
                    'content' => $content,
                ];
                $i++;
            }
        }

        return $result;
    }



    /* public function splitLegalText(string $text, array $headings): array
    {
        $pattern = '/(' . implode('|', array_map('preg_quote', $headings)) . ')/';

        // Split while keeping the matched headings using PREG_SPLIT_DELIM_CAPTURE
        $parts = preg_split($pattern, $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $result = [];
        for ($i = 0; $i < count($parts); $i++) {
            if (in_array(trim($parts[$i]), $headings)) {
                $title = trim($parts[$i]);
                $body = $parts[$i + 1] ?? '';
                $result[] = [
                    'title' => $title,
                    'content' => trim($body)
                ];
                $i++; // Skip body on next loop
            }
        }

        return $result;
    } */

    public function extractFromLegalStart(string $text): string
    {
        // Match Appellate Division followed by any line with (...) Jurisdiction
        if (preg_match('/Appellate Division\s*\n*\s*\(([^)]+ Jurisdiction)\)/i', $text, $match, PREG_OFFSET_CAPTURE)) {
            // Get position where the matched "Appellate Division\n(Jurisdiction)" starts
            $startPosition = $match[0][1];

            // Return everything from that position forward
            return substr($text, $startPosition);
        }

        // fallback to full text if not matched
        return $text;
    }


    public function extractDataFromLegalText(string $text): array
    {
        return [
            'division' => $this->extractFirstMatchingField($text, [
                '/\b(Appellate Division|High Court Division)\b/i',
            ]),

            'jurisdiction' => $this->extractFirstMatchingField($text, [
                '/\((Civil Jurisdiction)\)/i',
            ]),

            'judgename' => $this->extractFirstMatchingField($text, [
                '/([A-Z][a-zA-Z\s\.,;]+),\s*JJ\./',
            ]),

            'petition_no' => $this->extractPetitionNo($text),

            'parties' => $this->extractFirstMatchingField($text, [
                '/([A-Z][^\n]+?\s+(?:vs\.?|v\.?)\s+[^\n]+)/i',
            ]),

            /* 'decision_date' => $this->extractFirstMatchingField($text, [
                '/(?:Date of Judgment|Decided On)\s*:\s*(?:The\s+)?([0-9g]{1,2}(?:st|nd|rd|th)? of \w+, \d{4})/i',
            ]), */



            'content' => $this->extractContentFromJudgment($text),
        ];
    }


    public function extractPetitionNo(string $text): ?string
    {
        $pattern = '/\b(' .
            'Civil Appeal Nos?\.\s*[\d&\/\-\s]+ of \d{4}|' .
            'Civil Petition for Leave to Appeal No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'Civil Review Petition No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'Contempt Petition (?:Case\s+)?No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'Criminal Appeal No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'Civil Revision No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'Writ Petition No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'Income Tax Reference Application No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'Death Reference No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'Government Appeal No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'Appeal from Appellate Decree No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'Original Order No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'Criminal Revision No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'First Miscellaneous Appeal No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'Memorandum of Appeal from Original Decree No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'Civil Rule No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'Appeal from Original Order No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'First Appeal No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'Jail Appeal No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'Criminal Miscellaneous Case No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'Company Matter No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'Admiralty Suit No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'Matter No\.?\s*[\d&\/\-\s]+ of \d{4}' .
            ')\b/i';

        if (preg_match($pattern, $text, $match)) {
            return trim($match[1]);
        }

        return null;
    }



    /**
     * Return the first match from multiple patterns (first capture group).
     */
    protected function extractFirstMatchingField(string $text, array $patterns): ?string
    {
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return trim($matches[1]);
            }
        }
        return null;
    }

    /**
     * Extract everything after "Judgment" heading.
     */
    protected function extractContentFromJudgment(string $text): ?string
    {
        if (preg_match('/\bJudgment\b[\s:]*\n*(.+)/is', $text, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }


    public function splitParties(string $text): array
    {
        $text = trim($text);
        if (preg_match('/(.+?)\s+(vs\.?|v\.?)\s+(.+)/i', $text, $matches)) {
            return [
                'petitioners' => trim($matches[1]),
                'respondent' => trim($matches[3]),
            ];
        }
        return [
            'petitioners' => null,
            'respondent' => null,
        ];
    }


    public function extractVolume(string $text): ?int
    {
        // More flexible pattern: allows "Volume", "Vol.", optional colon, line breaks or hyphen
        if (preg_match('/\bVol(?:ume)?[\.]?\s*[:\-]?\s*([IVXLCDM\d]+)/i', $text, $match)) {
            $value = strtoupper(trim($match[1]));

            // Roman numeral to integer
            if (preg_match('/^[IVXLCDM]+$/', $value)) {
                return $this->romanToInt($value);
            }

            // Normal integer
            if (is_numeric($value)) {
                return (int) $value;
            }
        }

        return null;
    }

    private function romanToInt(string $roman): int
    {
        $map = ['M' => 1000, 'D' => 500, 'C' => 100, 'L' => 50, 'X' => 10, 'V' => 5, 'I' => 1];
        $value = 0;
        $last = 0;

        foreach (str_split(strrev($roman)) as $char) {
            $int = $map[$char];
            $value += ($int < $last) ? -$int : $int;
            $last = $int;
        }

        return $value;
    }

    public function extractPublishedYear(string $text): ?int
    {
        if (preg_match('/\b(19|20)\d{2}\b/', $text, $match)) {
            return (int) $match[0];
        }

        return null;
    }


    public function extractJudgmentDate(string $text): array
    {
        $text = preg_replace("/\r\n|\r/", "\n", $text);
        if (preg_match('/(?:Date of Judgment|Decided On)\s*:?\s*((?:.|\n){0,100}?\b\d{4})/i', $text, $match)) {
            $rawDateText = preg_replace('/-\s*\n\s*/', '', $match[1]);
            $rawDateText = preg_replace('/\s+/', ' ', $rawDateText);
            $result = $this->extractFullDateAndMonth($rawDateText);
            return $result ?? ['date' => null, 'month' => null];
        }

        return ['date' => null, 'month' => null];
    }



    public function extractFullDateAndMonth(string $text): ?array
    {
        $text = preg_replace('/Result/i', '', $text);
        $text = preg_replace('/-\s*\n\s*/', '', $text);
        $validMonths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $text = preg_replace_callback('/([A-Za-z]{3,})\s+([A-Za-z]+)/', function ($matches) use ($validMonths) {
            $joined = $matches[1] . $matches[2];
            foreach ($validMonths as $month) {
                if (stripos($month, $joined) === 0) {
                    return $joined; // Join broken month name
                }
            }
            return $matches[0]; // leave as is
        }, $text);

        // Step 4: Now match the full date
        /* if (preg_match('/The\s+(\d{1,2})(?:st|nd|rd|th)?\s+of\s+([A-Za-z]+),?\s*(\d{4})/i', $text, $matches)) {
            $fullDate = "The {$matches[1]} of {$matches[2]}, {$matches[3]}";
            $month = ucfirst(strtolower($matches[2]));
            return ['date' => $fullDate, 'month' => $month];
        } */
        if (preg_match('/The\s+(\d{1,2})(?:st|nd|rd|th)?\s+(?:of\s+)?([A-Za-z]+),?\s*(\d{4})?/i', $text, $matches)) {
            $day = $matches[1];
            $month = ucfirst(strtolower($matches[2]));
            $year = isset($matches[3]) ? $matches[3] : null;

            if ($year) {
                $fullDate = "The {$day} of {$month}, {$year}";
            } else {
                $fullDate = "The {$day} of {$month}";
            }

            return ['date' => $fullDate, 'month' => $month];
        }


        return ['date' => null, 'month' => null];
    }




    public function extractPageNumbers(string $text): array
    {
        // Match lines that contain only a number (likely page number), ignoring whitespace
        preg_match_all('/^\s*(\d{1,4})\s*$/m', $text, $matches);

        $pageNumbers = array_map('intval', $matches[1]);

        if (empty($pageNumbers)) {
            return [
                'start' => null,
                'end' => null,
            ];
        }

        return [
            'start' => $pageNumbers[0],            // First page number found in section
            'end' => end($pageNumbers),            // Last page number found in section
        ];
    }



    public function formatDate(?string $dateString): ?string
    {
        if (!$dateString) return null;

        try {
            return \Carbon\Carbon::parse($dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }


    public function extractCaseKeywords(string $text, array $stopwords = [], int $topN = 10): array
    {
        $text = strtolower($text);
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text); // remove punctuation
        $text = preg_replace('/\s+/', ' ', $text);
        $words = explode(' ', trim($text));

        $filteredWords = array_filter($words, function ($word) use ($stopwords) {
            return strlen($word) > 2 && !in_array($word, $stopwords);
        });

        $freq = array_count_values($filteredWords);
        arsort($freq);

        return array_slice(array_keys($freq), 0, $topN);
    }

    public function extractLegalSections(string $text): ?string
    {
        // Match common formats like: Section 302, Sec. 144, Article 102, etc.
        // preg_match_all('/\b(?:section|sec\.?|article)\s+[\dA-Za-z\-]+/i', $text, $matches);
        preg_match_all('/\b(?:section|sec\.?)\s+[\dA-Za-z\-]+/i', $text, $matches);

        if (!empty($matches[0])) {
            $unique = array_unique(array_map('trim', $matches[0]));
            return implode(', ', $unique);
        }

        return null;
    }
    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function update($id, $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function restore($id)
    {
        return $this->repository->restore($id);
    }

    public function forceDelete($id)
    {
        return $this->repository->forceDelete($id);
    }

    public function __destruct()
    {
        if ($this->googleClient) {
            $this->googleClient->close();
        }
    }
}
