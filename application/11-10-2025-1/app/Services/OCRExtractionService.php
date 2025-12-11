<?php

namespace App\Services;

use App\Repositories\Contracts\OCRExtractionRepositoryInterface;
use Google\Cloud\Vision\V1\Image;
use Google\Cloud\Vision\V1\Feature;
use Google\Cloud\Vision\V1\AnnotateImageRequest;
use Google\Cloud\Vision\V1\BatchAnnotateImagesRequest;
// use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Client\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Feature\Type as FeatureType;
use Google\Cloud\Vision\V1\TextAnnotation\DetectedBreak\BreakType;
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

    public function index($filters = [])
    {
        return $this->repository->index($filters);
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
    } */


    /* private function extractTextFromImage(string $filePath): string
    {
        $content = file_get_contents($filePath);
        $image = (new Image())->setContent($content);
        $feature = (new Feature())->setType(Feature\Type::DOCUMENT_TEXT_DETECTION);

        $request = (new AnnotateImageRequest())->setImage($image)->setFeatures([$feature]);
        $batchRequest = (new BatchAnnotateImagesRequest())->setRequests([$request]);
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
                $midpoint = $page->getWidth() / 2;

                foreach ($page->getBlocks() as $block) {
                    $vertices = $block->getBoundingBox()->getVertices();
                    $x = $vertices[0]->getX();
                    $y = $vertices[0]->getY();

                    $paragraphText = '';
                    foreach ($block->getParagraphs() as $paragraph) {
                        if (method_exists($paragraph, 'getText')) {
                            $paragraphText .= $paragraph->getText() . "\n";
                        } else {
                            foreach ($paragraph->getWords() as $word) {
                                foreach ($word->getSymbols() as $symbol) {
                                    $paragraphText .= $symbol->getText();
                                }
                                $paragraphText .= ' ';
                            }
                            $paragraphText .= "\n";
                        }
                    }

                    $entry = [
                        'y' => $y,
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

        // Sort blocks vertically
        usort($leftColumnBlocks, fn($a, $b) => $a['y'] <=> $b['y']);
        usort($rightColumnBlocks, fn($a, $b) => $a['y'] <=> $b['y']);

        return trim(
            implode("\n", array_column($leftColumnBlocks, 'text')) .
                "\n\n" .
                implode("\n", array_column($rightColumnBlocks, 'text'))
        );
    } */


    /// Workable Version

    // private function extractTextFromImage(string $filePath): string
    // {
    //     $content = file_get_contents($filePath);
    //     $image = (new Image())->setContent($content);
    //     $feature = (new Feature())->setType(Feature\Type::DOCUMENT_TEXT_DETECTION);

    //     $request = (new AnnotateImageRequest())->setImage($image)->setFeatures([$feature]);
    //     $batchRequest = (new BatchAnnotateImagesRequest())->setRequests([$request]);
    //     $response = $this->googleClient->batchAnnotateImages($batchRequest);

    //     $leftColumnBlocks = [];
    //     $rightColumnBlocks = [];

    //     foreach ($response->getResponses() as $res) {
    //         if ($res->hasError()) {
    //             throw new \Exception($res->getError()->getMessage());
    //         }

    //         $annotation = $res->getFullTextAnnotation();
    //         if (!$annotation) {
    //             continue;
    //         }

    //         foreach ($annotation->getPages() as $page) {
    //             $midpoint = $page->getWidth() / 2;

    //             foreach ($page->getBlocks() as $block) {
    //                 $vertices = $block->getBoundingBox()->getVertices();
    //                 $x = $vertices[0]->getX();
    //                 $y = $vertices[0]->getY();

    //                 $paragraphText = '';

    //                 foreach ($block->getParagraphs() as $paragraph) {
    //                     foreach ($paragraph->getWords() as $word) {
    //                         foreach ($word->getSymbols() as $symbol) {
    //                             $paragraphText .= $symbol->getText();

    //                             // Detect break type from symbol property
    //                             $property = $symbol->getProperty();
    //                             if ($property && $property->getDetectedBreak()) {
    //                                 $breakType = $property->getDetectedBreak()->getType();
    //                                 switch ($breakType) {
    //                                     case 1: // SPACE
    //                                         $paragraphText .= ' ';
    //                                         break;
    //                                     case 3: // LINE_BREAK
    //                                         $paragraphText .= "\n";
    //                                         break;
    //                                     case 5: // PARAGRAPH_END
    //                                         $paragraphText .= "\n\n";
    //                                         break;
    //                                 }
    //                             }
    //                         }
    //                     }
    //                 }

    //                 $entry = [
    //                     'y' => $y,
    //                     'text' => trim($paragraphText),
    //                 ];

    //                 if ($x < $midpoint) {
    //                     $leftColumnBlocks[] = $entry;
    //                 } else {
    //                     $rightColumnBlocks[] = $entry;
    //                 }
    //             }
    //         }
    //     }

    //     // Sort blocks vertically
    //     usort($leftColumnBlocks, fn($a, $b) => $a['y'] <=> $b['y']);
    //     usort($rightColumnBlocks, fn($a, $b) => $a['y'] <=> $b['y']);

    //     // Merge text from both columns
    //     $leftText = implode("\n", array_column($leftColumnBlocks, 'text'));
    //     $rightText = implode("\n", array_column($rightColumnBlocks, 'text'));
    //     $fullText = $leftText . "\n\n" . $rightText;

    //     // Fix hyphenated line breaks
    //     $fullText = preg_replace("/-\s*\n\s*/", "", $fullText);

    //     // Clean multiple spaces/newlines
    //     $fullText = preg_replace("/[ \t]+/", " ", $fullText);
    //     $fullText = preg_replace("/\n{3,}/", "\n\n", $fullText);

    //     return trim($fullText);
    // }



    private function extractTextFromImage(string $filePath): string
    {
        $content = file_get_contents($filePath);
        $image = (new Image())->setContent($content);
        $feature = (new Feature())->setType(Feature\Type::DOCUMENT_TEXT_DETECTION);

        $request = (new AnnotateImageRequest())->setImage($image)->setFeatures([$feature]);
        $batchRequest = (new BatchAnnotateImagesRequest())->setRequests([$request]);
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
                $midpoint = $page->getWidth() / 2;

                foreach ($page->getBlocks() as $block) {
                    $vertices = $block->getBoundingBox()->getVertices();
                    $x = $vertices[0]->getX();
                    $y = $vertices[0]->getY();

                    $paragraphText = '';

                    foreach ($block->getParagraphs() as $paragraph) {
                        foreach ($paragraph->getWords() as $word) {
                            foreach ($word->getSymbols() as $symbol) {
                                $paragraphText .= $symbol->getText();

                                // Detect break type from symbol property
                                $property = $symbol->getProperty();
                                if ($property && $property->getDetectedBreak()) {
                                    $breakType = $property->getDetectedBreak()->getType();
                                    switch ($breakType) {
                                        case 1: // SPACE
                                            $paragraphText .= ' ';
                                            break;
                                        case 3: // LINE_BREAK
                                            $paragraphText .= "\n";
                                            break;
                                        case 5: // PARAGRAPH_END
                                            $paragraphText .= "\n\n";
                                            break;
                                    }
                                }
                            }
                        }
                    }

                    $entry = [
                        'y' => $y,
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

        // Sort blocks vertically
        usort($leftColumnBlocks, fn($a, $b) => $a['y'] <=> $b['y']);
        usort($rightColumnBlocks, fn($a, $b) => $a['y'] <=> $b['y']);

        // Merge text from both columns
        $leftText = implode("\n", array_column($leftColumnBlocks, 'text'));
        $rightText = implode("\n", array_column($rightColumnBlocks, 'text'));
        $fullText = $leftText . "\n\n" . $rightText;

        // Merge both columns into one list
        /* $allBlocks = array_merge($leftColumnBlocks, $rightColumnBlocks);
        // Sort by Y position
        usort($allBlocks, fn($a, $b) => $a['y'] <=> $b['y']);
        // Merge into text
        $fullText = implode("\n", array_column($allBlocks, 'text')); */


        // Fix hyphenated line breaks
        $fullText = preg_replace("/-\s*\n\s*/", "", $fullText);

        // Clean multiple spaces/newlines
        $fullText = preg_replace("/[ \t]+/", " ", $fullText);
        $fullText = preg_replace("/\n{3,}/", "\n\n", $fullText);

        // Convert plain text newlines into HTML
        // Double line breaks → paragraph
        $fullText = preg_replace("/\n{2,}/", "</p><p>", $fullText);

        // Single line break → <br>
        $fullText = preg_replace("/\n/", "<br>", $fullText);

        // Wrap everything inside <p>
        $fullText = "<p>" . $fullText . "</p>";

        return trim($fullText);
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

    /* private function extractTextFromPdf($path)
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($path);
        $text = trim($pdf->getText());

        if (!empty($text)) {
            return $text; // PDF has selectable text
        }

        // Otherwise, scanned PDF – use OCR
        return $this->extractTextFromPdfUsingOcr($path);
    } */

    private function extractTextFromPdf($path)
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($path);
        $text = trim($pdf->getText());

        if (!empty($text)) {
            return $text;  // plain text, whole PDF
        }

        // fallback to OCR
        return $this->extractTextFromPdfUsingOcr($path);
    }


    private function extractTextFromPdfUsingOcr($pdfPath)
    {
        $imagick = new \Imagick();
        $imagick->setResolution(200, 200); // Slightly lower DPI for faster conversion
        $imagick->readImage($pdfPath);

        $text = '';
        $uniqueId = (string) Str::uuid();
        $outputDir = storage_path("app/ocr_pages/{$uniqueId}");
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        $pageIndex = 1;
        // New Code
        // $pageTexts = [];

        foreach ($imagick as $page) {
            $page->setImageFormat('jpeg');
            $page->setCompressionQuality(70); // Lower quality for faster OCR
            $imagePath = "{$outputDir}/page_{$pageIndex}.jpg";
            $page->writeImage($imagePath);

            // OLd Code
            $text .= $this->extractTextFromImage($imagePath) . "\n";
            $pageIndex++;


            // New Code: Store text keyed by page number
            // $pageTexts[$pageIndex] = $this->extractTextFromImage($imagePath);
        }

        $imagick->clear();
        $imagick->destroy();

        // Optional cleanup:
        // File::deleteDirectory($outputDir);

        return trim($text);

        // return $pageTexts;  // New Code: return array: [pageNumber => extractedText]
    }

    // Workable Version
    /* public function splitLegalText(string $text, array $headings): array
    {
        $endKeywords = ['SMZH', 'S.M.Z.H', 'KAK', 'Ed.', 'End.'];

        $jurisdictions = [
            'Criminal Revisional Jurisdiction',
            'Civil Revisional Jurisdiction',
            'Special Revisional Jurisdiction',
            'Special Original Jurisdiction'
        ];



        $jurisdictionPattern = implode('|', array_map('preg_quote', $jurisdictions));
        $pattern = '/(' . implode('|', array_map('preg_quote', $headings)) . ')\s*\(\s*(' . $jurisdictionPattern . ')\s*\)/i';

        // Split text on headings + jurisdiction pattern, keeping delimiter parts
        $parts = preg_split($pattern, $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        $result = [];

        // Since pattern has 2 capture groups: heading and jurisdiction, the parts array looks like:
        // [beforeText, heading1, jurisdiction1, textAfter1, heading2, jurisdiction2, textAfter2, ...]

        // So iterate with step of 3 starting from 1 (heading)
        for ($i = 1; $i < count($parts); $i += 3) {
            $title = trim($parts[$i] . ' (' . $parts[$i + 1] . ')');
            $body = $parts[$i + 2] ?? '';

            // Split body lines and stop on end keywords
            $lines = preg_split('/\r?\n/', $body);
            $sectionContentLines = [];

            foreach ($lines as $line) {
                $sectionContentLines[] = $line;

                foreach ($endKeywords as $endKeyword) {
                    if (preg_match('/\b' . preg_quote($endKeyword, '/') . '\b/i', $line)) {
                        break 2;
                    }
                }
            }

            $content = trim(implode("\n", $sectionContentLines));

            $result[] = [
                'title' => $title,
                'content' => $content,
            ];
        }

        return $result;
    } */


    public function splitLegalText(string $text, array $headings): array
    {
        $endKeywords = ['SMZH', 'S.M.Z.H', 'KAK', 'Ed.', 'End.'];

        $jurisdictions = [
            'Criminal Revisional Jurisdiction',
            'Civil Revisional Jurisdiction',
            'Special Revisional Jurisdiction',
            'Special Original Jurisdiction',
            'Civil Miscellaneous Jurisdiction',
            'Criminal Appellate Jurisdiction',
            'Criminal Jurisdiction'
        ];

        $jurisdictionPattern = implode('|', array_map('preg_quote', $jurisdictions));
        $pattern = '/(' . implode('|', array_map('preg_quote', $headings)) . ')\s*(?:<br\s*\/?>|\s)*\(\s*(' . $jurisdictionPattern . ')\s*\)/i';


        // Split text on headings + jurisdiction pattern, keeping delimiter parts
        $parts = preg_split($pattern, $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        $result = [];

        for ($i = 1; $i < count($parts); $i += 3) {
            $title = trim($parts[$i] . ' (' . $parts[$i + 1] . ')');
            $body = $parts[$i + 2] ?? '';

            // 🔥 Normalize HTML into "lines"
            $lines = preg_split('/<br\s*\/?>|<\/p>\s*<p>/i', $body);

            $sectionContentLines = [];

            foreach ($lines as $line) {
                $sectionContentLines[] = $line;

                foreach ($endKeywords as $endKeyword) {
                    if (preg_match('/\b' . preg_quote($endKeyword, '/') . '\b/i', $line)) {
                        break 2;
                    }
                }
            }

            $content = trim(implode("<br>", $sectionContentLines));

            $result[] = [
                'title' => $title,
                'content' => "<p>" . $content . "</p>",
            ];
        }

        return $result;
    }


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


    /* public function extractDataFromLegalText(string $text): array
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

            // thsi will be remove
            'decision_date' => $this->extractFirstMatchingField($text, [
                '/(?:Date of Judgment|Decided On)\s*:\s*(?:The\s+)?([0-9g]{1,2}(?:st|nd|rd|th)? of \w+, \d{4})/i',
            ]),

            'content' => $this->extractContentFromJudgment($text),
        ];
    } */


    /* public function extractDataFromLegalText(string $text, string $title): array
    {
        return [
            'division' => $this->extractFirstMatchingField($title, [
                '/\b(Appellate\s+Division|High\s+Court\s+Division)\b/i',
            ]),

            'jurisdiction' => $this->extractFirstMatchingField($title, [
                '/\(\s*([A-Za-z\s]+Jurisdiction)\s*\)/i',
            ]),

            'judgename' => $this->extractFirstMatchingField($text, [
                // '/([A-Z][A-Za-z\.\-\s,]+)\s*,?\s*(?:JJ?|C\.J\.)/i'
                '/([A-Z][A-Za-z\.\-\s]+?,\s*J{1,2})(?=\s|$)/i'
            ]),


            'petition_no' => $this->extractPetitionNo($text),

            'parties' => $this->extractFirstMatchingField($text, [
                '/([A-Z][^\n]+?\s+(?:vs\.?|v\.?)\s+[^\n]+)/i',
            ]),

            'case_type' => extractField($text, [
                '/\b(Criminal|Civil|Writ|Family|Tax)\b/i',
            ]),
            'verdict' => extractField($text, [
                '/The court (.+?)\./i',
            ]),
            'summary' => mb_substr($text, 0, 300),
            'ref_law' => extractField($text, [
                '/under section\s+([\d\w\s]+ of [A-Za-z\s]+)/i',
            ]),
            'ref_journal' => extractField($text, [
                '/reported in\s+([^\n\.]+)/i',
            ]),
            
            'content' => $this->extractContentFromJudgment($text),
        ];
    } */


    // public function extractDataFromLegalText($text, $title, $year, $volume): array
    // {
    //     // Step 1: fix hyphenated line breaks
    //     $text = preg_replace('/([A-Za-z])-\s+([A-Za-z])/', '$1$2', $text);
    //     $division = 'High Court Division';
    //     return [
    //         'division' => $this->extractFirstMatchingField($title, [
    //             '/\b(Appellate\s+Division|High\s+Court\s+Division)\b/i',
    //         ]),

    //         'jurisdiction' => $this->extractFirstMatchingField($title, [
    //             '/\(\s*([A-Za-z\s]+Jurisdiction)\s*\)/i',
    //         ]),

    //         'judgename' => $this->extractFirstMatchingField($text, [
    //             '/([A-Z][A-Za-z\.\-\s]+?),\s*J{1,2}(?=\s|$)/i'
    //         ]),

    //         'petition_no' => $this->extractPetitionNo($text),

    //         /* 'parties' => $this->extractFirstMatchingField($text, [
    //             '/([A-Z][^\n]+?\s+(?:vs\.?|v\.?)\s+[^\n]+)/i',
    //         ]), */
    //         'ref_law' => $this->extractFirstMatchingField($text, [
    //             '/under section\s+([\d\w\s]+ of [A-Za-z\s]+)/i',
    //         ]),
    //         'parties' => $this->extractParties($text, $division, $year, $volume),
    //         'extractPetitionersAndRespondents' => $this->extractPetitionersAndRespondents($text),

    //         'content' => $this->extractContentFromJudgment($text),
    //     ];
    // }


    public function getDivision($title)
    {
        return $this->extractFirstMatchingField($title, [
            '/\b(Appellate\s+Division|High\s+Court\s+Division)\b/i',
        ]);
    }

    public function extractDataFromLegalText(string $text, $title, $year, $volume): array
    {
        // Fix hyphenated line breaks in HTML
        $text = preg_replace('/([A-Za-z])-\s*<br\s*\/?>\s*([A-Za-z])/', '$1$2', $text);

        // Convert HTML to temporary plain-text version for regex matching
        $regexText = preg_replace('/<br\s*\/?>/i', "\n", $text);
        $regexText = preg_replace('/<\/?p>/i', '', $regexText);
        $regexText = preg_replace('/[ \t]+/', ' ', $regexText);

        $division = $this->getDivision($title);

        return [
            /* 'division' => $this->extractFirstMatchingField($title, [
                '/\b(Appellate\s+Division|High\s+Court\s+Division)\b/i',
            ]), */

            'jurisdiction' => $this->extractFirstMatchingField($title, [
                '/\(\s*([A-Za-z\s]+Jurisdiction)\s*\)/i',
            ]),

            'judgename' => $this->extractFirstMatchingField($regexText, [
                '/([A-Z][A-Za-z\.\-\s]+?),\s*J{1,2}(?=\s|$)/i'
            ]),
            'case_type' => $this->extractFirstMatchingField($text, [
                '/\b(Criminal|Civil|Writ|Family|Tax)\b/i',
            ]),

            'petition_no' => $this->extractPetitionNo($regexText),

            'ref_law' => $this->extractActsAndRules($text),
            'result' => $this->extractResultText($text),

            'parties' => $this->extractParties($regexText, $division, $year, $volume),

            'extractPetitionersAndRespondents' => $this->extractPetitionersAndRespondents($regexText),

            // Keep full HTML content
            'content' => $this->extractContentFromJudgment($text),
        ];
    }

    /* public function extractSubjectText(string $html): ?string
    {
        // Normalize HTML
        $text = preg_replace('/<br\s*\/?>/i', "\n", $html);
        $text = preg_replace('/<\/?p>/i', "\n", $text);
        $text = preg_replace("/\r\n|\r/", "\n", $text);
        $text = preg_replace("/\n+/", "\n", $text);
        $text = trim($text);

        // Match after Section (any variant) up to Judgment
        $pattern = '/Section\s*-{0,2}\s*:?.*?\n(.*?)\n\s*Judgment\b/sim';

        if (preg_match($pattern, $text, $matches)) {
            $subjectText = trim($matches[1]);
            return $subjectText ?: null;
        }

        return null;
    } */


    public function extractSubjectText(string $html): ?string
    {
        // Normalize HTML
        $text = preg_replace('/<br\s*\/?>/i', "\n", $html);
        $text = preg_replace('/<\/?p>/i', "\n", $text);
        $text = preg_replace("/\r\n|\r/", "\n", $text);
        $text = preg_replace("/\n+/", "\n", $text);
        $text = trim($text);

        // Match after Section (any variant) up to Judgment
        $pattern = '/Section\s*-{0,2}\s*:?.*?\n(.*?)\n\s*Judgment\b/sim';

        if (preg_match($pattern, $text, $matches)) {
            $subjectText = trim($matches[1]);

            // Find the last Para and cut after it
            if (preg_match_all('/\((?:Para|Paras)[^\)]*\)/i', $subjectText, $paraMatches, PREG_OFFSET_CAPTURE)) {
                $lastPara = end($paraMatches[0]);
                $endPos = $lastPara[1] + strlen($lastPara[0]);
                $subjectText = substr($subjectText, 0, $endPos);
            }

            // Remove all Para patterns from the subject
            $subjectText = preg_replace('/\((?:Para|Paras)[^\)]*\)/i', '', $subjectText);

            return trim($subjectText) ?: null;
        }

        return null;
    }




    /* protected function extractActsAndRules(string $html): ?string
    {
        $acts = [];
        $rules = [];
        $ordinances = [];
        $orders = [];

        // 1) Normalize HTML
        $text = preg_replace('/<br\s*\/?>/i', ' ', $html); // <br> -> space
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        // 2) Match everything after "Date of Judgment :"
        if (preg_match('/Date of Judgment\s*:\s*(.*?)(?=\sSection\b|$)/i', $text, $matches)) {
            $snippet = trim($matches[1]);

            // 3) Unified regex for Acts, Rules, Ordinances, Orders
            $pattern = '/[A-Z][A-Za-z\s\(\)]+(?:Act|Rules|Ordinance|Order)(?:\s*,?\s*\(?\d{4}\)?)?(?:\s*\(.*?\))?/';

            if (preg_match_all($pattern, $snippet, $matches)) {
                foreach ($matches[0] as $m) {
                    $m = trim($m);
                    if (stripos($m, 'Act') !== false && !$acts) $acts[] = $m;
                    if (stripos($m, 'Rules') !== false && !$rules) $rules[] = $m;
                    if (stripos($m, 'Ordinance') !== false && !$ordinances) $ordinances[] = $m;
                    if (stripos($m, 'Order') !== false && !$orders) $orders[] = $m;
                }
            }
        }

        // 4) Combine results
        $result = [];
        if (!empty($acts)) $result[] = $acts[0];
        if (!empty($rules)) $result[] = $rules[0];
        if (!empty($ordinances)) $result[] = $ordinances[0];
        if (!empty($orders)) $result[] = $orders[0];

        return $result ? implode(' | ', $result) : null;
    } */


    protected function extractActsAndRules(string $html): ?string
    {
        // Normalize <br> and line breaks
        $text = preg_replace('/<br\s*\/?>/i', "\n", $html);
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\r\n|\r/', "\n", $text);
        $text = preg_replace('/\n+/', "\n", $text);
        $lines = explode("\n", $text);

        $collecting = false;
        $snippetLines = [];

        // Section pattern
        $sectionPattern = '/Section\s*(?:-|:|--)?\s*\d*/i';

        // Legal patterns
        $patterns = [
            '/[A-Z][A-Za-z\s]+Act(?:\s*,?\s*\(?\d{4}\)?)?/',
            '/[A-Z][A-Za-z\s]+Rules(?:\s*,?\s*\(?\d{4}\)?)?/',
            '/[A-Z][A-Za-z\s\(\),]+Ordinance(?:\s*,?\s*\(?\d{4}\)?)?/',
            '/[A-Z][A-Za-z\s\(\),]+Order(?:\s*[A-Z]+\d*|\s*,?\s*\(?\d{4}\)?)?/'
        ];

        foreach ($lines as $line) {
            $line = trim($line);
            if (!$line) continue;

            // Start after Date of Judgment
            if (!$collecting && preg_match('/Date of Judgment\s*:/i', $line)) {
                $collecting = true;
                continue; // skip this line
            }

            // Stop at first Section line
            if ($collecting && preg_match($sectionPattern, $line)) {
                break;
            }

            // Check if line matches any legal pattern
            foreach ($patterns as $pat) {
                if (preg_match($pat, $line)) {
                    $snippetLines[] = $line;
                    break 2; // stop scanning further lines; we only want the first match
                }
            }
        }

        if (empty($snippetLines)) return null;

        return trim($snippetLines[0]);
    }






    /* protected function extractActsAndRules(string $html): ?string
    {
        $acts = [];
        $rules = [];

        // Normalize <br> tags and line breaks
        $text = preg_replace('/<br\s*\/?>/i', "\n", $html);
        $text = preg_replace("/\r\n|\r/", "\n", $text);
        $text = preg_replace("/\n+/", "\n", $text);

        // Section patterns
        $sectionPattern = [
            'Section\s*-\s*.*',
            'Section\s*:\s*.*',
            'Section\s*--\s*.*',
            'Section\b'
        ];
        $sectionRegex = implode('|', $sectionPattern);

        // Extract content after Date of Judgment up to first Section
        if (preg_match('/Date of Judgment.*?\n(.*?)\n(?:' . $sectionRegex . ')/is', $text, $matches)) {
            $snippet = $matches[1];

            // Split snippet into lines
            $lines = preg_split('/\n/', $snippet);

            foreach ($lines as $line) {
                $line = trim($line);
                if (!$line) continue;

                // First Act
                if (!$acts && preg_match('/[A-Z][A-Za-z\s]+Act(?:\s*,?\s*\(?\d{4}\)?)?/', $line, $actMatch)) {
                    $acts[] = trim($actMatch[0]);
                }

                // First Rule
                if (!$rules && preg_match('/[A-Z][A-Za-z\s]+Rules(?:\s*,?\s*\(?\d{4}\)?)?/', $line, $ruleMatch)) {
                    $rules[] = trim($ruleMatch[0]);
                }

                // Stop if both found
                if ($acts && $rules) break;
            }
        }

        // Combine Act and Rule as string
        $result = '';
        if (!empty($acts)) $result .= $acts[0];
        if (!empty($rules)) $result .= $acts ? ' | ' . $rules[0] : $rules[0];

        return $result ?: null;
    } */


    protected function extractResultText(string $html): ?string
    {
        // Normalize <br> tags to newlines
        $text = preg_replace('/<br\s*\/?>/i', "\n", $html);
        $text = preg_replace("/\r\n|\r/", "\n", $text);
        $text = preg_replace("/\n+/", "\n", $text);

        // Find "Date of Judgment"
        if (preg_match('/Date of Judgment/i', $text, $matches, PREG_OFFSET_CAPTURE)) {
            $offset = $matches[0][1] + strlen($matches[0][0]);
            $afterDateText = substr($text, $offset);

            // Split remaining text into lines
            $lines = explode("\n", $afterDateText);

            $resultLines = [];
            $found = false;

            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') continue;

                // Skip lines that are part of date
                if (preg_match('/^(:?\s*The\s+)?\d{1,2}(st|nd|rd|th)?\s*[A-Za-z]+,?\s*(\d{4})?$/i', $line)) continue;
                if (preg_match('/^\s*\d{4}\s*$/', $line)) continue;

                // Stop at next Section heading
                if (preg_match('/^Section\b/i', $line)) break;

                if (!$found) {
                    // First meaningful line
                    $resultLines[] = $line;
                    $found = true;
                } else {
                    // Optionally include next line if it's not a Section
                    $resultLines[] = $line;
                    break; // only next line after first
                }
            }

            if ($resultLines) {
                // Combine lines
                $resultText = implode(' ', $resultLines);
                // Remove "Result" word if present
                $resultText = preg_replace('/\bResult\b[:]?/i', '', $resultText);
                return trim($resultText);
            }
        }

        return null;
    }








    /* protected function extractPetitionersAndRespondents(string $text)
    {
        $petitioners = [];
        $respondents = [];

        $petitionerEndings = [
            'for the Petitioners',
            'for the petitioner',
            'for the Appellants',
            'for the Appellant',
            'for the Plaintiff-Petitioner',
            'for the Defendant Appellants',
            'for the Plaintiff-Appellants',
            'for the Accused Applicant',
            'for the Accused Petitioner',
            'for the State',
            'for the Condemned Petitioner',
            'for the Defd-Appellant'
        ];

        $respondentEndings = [
            'for the Respondent',
            'for the Respondents',
            'the Respondent',
            'for the Opposite Party',
            'for the Opposite Parties',
            'for the Defendant Opposite Part',
            'for the State',
            'for the Condemned Prisoner',
            'for the Plaintiff-Respondents'
        ];

        // Step 1: extract only the text before "Judgment"
        if (!preg_match('/^(.*?)(^\s*Judgment\s*$)/ms', $text, $match)) {
            return ['petitioners' => null, 'respondents' => null];
        }

        $beforeJudgmentText = $match[1];

        // Step 2: Build regex for petitioners/respondents (multi-line safe)
        $petPattern  = '/([^\n]+?)\s*(?:' . implode('|', array_map('preg_quote', $petitionerEndings)) . ')\.?/i';
        $respPattern = '/([^\n]+?)\s*(?:' . implode('|', array_map('preg_quote', $respondentEndings)) . ')\.?/i';

        // Step 3: Petitioners
        if (preg_match_all($petPattern, $beforeJudgmentText, $petMatches)) {
            foreach ($petMatches[1] as $name) {
                $name = trim(preg_replace('/\s+/', ' ', $name)); // clean multiple spaces/newlines
                if ($name) {
                    $petitioners[] = rtrim($name, " ,;");
                }
            }
        }

        // Step 4: Respondents
        if (preg_match_all($respPattern, $beforeJudgmentText, $respMatches)) {
            foreach ($respMatches[1] as $name) {
                $name = trim(preg_replace('/\s+/', ' ', $name));
                if ($name) {
                    $respondents[] = rtrim($name, " ,;");
                }
            }
        }

        return [
            'petitioners' => $petitioners ?: null,
            'respondents' => $respondents ?: null
        ];
    } */

    protected function extractPetitionersAndRespondents(string $text)
    {
        // --- Step 1: Convert HTML to Plain Text ---
        $text = str_replace(['<br>', '<br/>', '<br />'], "\n", $text);
        $text = str_replace('</p><p>', "\n\n", $text);
        $text = strip_tags($text);

        $petitioners = [];
        $respondents = [];

        // Order the endings from longest to shortest to ensure correct matching
        $petitionerEndings = [
            'for the Plaintiff-Appellants',
            'for the Defendant Appellants',
            'for the Plaintiff-Petitioner',
            'for the Accused Applicant',
            'for the Accused Petitioner',
            'for the Condemned Petitioner',
            'for the Appellants',
            'for the Appellant',
            'for the Petitioners',
            'for the petitioner',
            'for the State',
            'for the Defd-Appellant'
        ];

        // Order the endings from longest to shortest to ensure correct matching
        $respondentEndings = [
            'for the Plaintiff-Respondents',
            'for the Defendant Opposite Part',
            'for the Opposite Parties',
            'for the Condemned Prisoner',
            'for the Opposite Party',
            'for the Respondents',
            'for the Respondent',
            'the Respondent',
            'for the State'
        ];

        // Sort endings by length, longest first
        usort($petitionerEndings, fn($a, $b) => strlen($b) <=> strlen($a));
        usort($respondentEndings, fn($a, $b) => strlen($b) <=> strlen($a));

        // Step 2: Extract only the text before "Judgment"
        if (!preg_match('/^(.*?)(^\s*Judgment\s*$)/ms', $text, $match)) {
            return ['petitioners' => null, 'respondents' => null];
        }

        $beforeJudgmentText = $match[1];

        // Step 3: Build regex for petitioners/respondents (multi-line safe)
        $petPattern  = '/([^\n]+?)\s*(?:' . implode('|', array_map('preg_quote', $petitionerEndings)) . ')\s*[\.,;]?/i';
        $respPattern = '/([^\n]+?)\s*(?:' . implode('|', array_map('preg_quote', $respondentEndings)) . ')\s*[\.,;]?/i';

        // Step 4: Petitioners
        if (preg_match_all($petPattern, $beforeJudgmentText, $petMatches)) {
            foreach ($petMatches[0] as $match) { // full match includes "for the petitioner" dynamically
                $nameLine = trim(preg_replace('/\s+/', ' ', $match)); // normalize spaces
                if ($nameLine) {
                    $petitioners[] = rtrim($nameLine, " ,;");
                }
            }
        }

        if (preg_match_all($respPattern, $beforeJudgmentText, $respMatches)) {
            foreach ($respMatches[0] as $match) { // full match includes dynamic text
                $nameLine = trim(preg_replace('/\s+/', ' ', $match));
                if ($nameLine) {
                    $respondents[] = rtrim($nameLine, " ,;");
                }
            }
        }


        return [
            'petitioners' => $petitioners ?: null,
            'respondents' => $respondents ?: null
        ];
    }


    /* protected function extractParties(string $text, string $division, int $year, int $volume): ?string
    {
        // Division code
        $divisionCode = strtoupper(str_starts_with($division, 'Appellate') ? 'AD' : 'HCD');

        // Escape for regex
        $yearPattern = preg_quote((string)$year, '/');
        $volumePattern = preg_quote((string)$volume, '/');

        // Pattern to handle:
        // - (1993)BLD(AD) Party v. Party
        // - 20 BLD(AD)(2000) Party v. Party
        // - Possible OCR spaces/extra parentheses
        $pattern = "/
        (?:\(?{$yearPattern}\)?\s*BLD\s*\(\s*{$divisionCode}\s*\)     # (1993)BLD(AD)
        |
        {$volumePattern}\s*BLD\s*\(\s*{$divisionCode}\s*\)\s*\(\s*{$yearPattern}\s*\)  # 20 BLD(AD)(2000)
        )
        [\s\r\n]+
        ([A-Z][^\n]+?\s+(?:v\.|vs\.?)\s+[^\n]+)                       # Capture parties
    /ix";

        if (preg_match($pattern, $text, $matches)) {
            return trim($matches[1]);
        }

        return null;
    } */



    protected function extractParties(string $text, string $division, int $year, int $volume): ?string
    {
        // Division code
        $divisionCode = strtoupper(str_starts_with($division, 'Appellate') ? 'AD' : 'HCD');

        // Escape for regex
        $yearPattern = preg_quote((string)$year, '/');
        $volumePattern = preg_quote((string)$volume, '/');

        // Normalize HTML to plain text-like separators
        $normalizedText = preg_replace('/<br\s*\/?>|<\/p>|<p>/i', "\n", $text);



        // Pattern to match:
        // - (1993)BLD(AD) Party v. Party
        // - 20 BLD(AD)(2000) Party v. Party
        // - Allows spaces, newlines (from <br>/<p>) between BLD and party line

        $pattern = "/
            (?:\(?{$yearPattern}\)?\s*(?:BLD|BLO)\s*\(\s*{$divisionCode}\s*\) |
            {$volumePattern}\s*(?:BLD|BLO)\s*\(\s*{$divisionCode}\s*\)\s*\(?{$yearPattern}\)?)
            [\s\r\n]+
            (.+?(?:v\.|vs\.?)\s+.+)
        /ix";




        /* $pattern = "/
            (?:\(?{$yearPattern}\)?\s*(?:BLD|BLO)\s*\(\s*{$divisionCode}\s*\) 
            |
            {$volumePattern}\s*(?:BLD|BLO)\s*\(\s*{$divisionCode}\s*\)\s*\(\s*{$yearPattern}\s*\)
            )
            [\s\r\n]+(.+?(?:v\.|vs\.?)\s+.+)/ix"; */
        // dd($pattern);


        if (preg_match($pattern, $normalizedText, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }


    /* public function extractPetitionNo(string $text): ?string
    {
        $pattern = '/\b(' .
            'Civil Appeal Nos?\.\s*[\d&\/\-\s]+ of \d{4}|' .
            'Civil Petition for Leave to Appeal No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'Civil Review Petition No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'Contempt Petition (?:Case\s+)?No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'Criminal Appeal No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'Civil Revision No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'Writ Petition No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'Converted from Civil Revision No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
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

        // return preg_match($pattern, $text, $match);
        if (preg_match($pattern, $text, $match)) {
            return trim($match[1]);
        }

        return null;
    } */


    public function extractPetitionNo(string $text): ?string
    {
        // 1) Normalize HTML line breaks and tags into spaces
        $clean = preg_replace('/<br\s*\/?>/i', ' ', $text);   // <br>, <br/> etc -> space
        $clean = strip_tags($clean);                          // remove other tags
        $clean = html_entity_decode($clean, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $clean = preg_replace('/\s+/', ' ', $clean);          // collapse whitespace
        $clean = trim($clean);

        // 2) Your original regex (unchanged) — matches "X No. 236 of 2001"
        $pattern = '/\b(' .
            'Civil Appeal Nos?\.\s*[\d&\/\-\s]+ of \d{4}|' .
            'Civil Appeal No?\.\s*[\d&\/\-\s]+ of \d{4}|' .
            'Civil Petition for Leave to Appeal No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'Civil Review Petition No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'Contempt Petition (?:Case\s+)?No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'Criminal Appeal No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'Civil Revision No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'Writ Petition No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
            'Converted from Civil Revision No\.?\s*[\d&\/\-\s]+ of \d{4}|' .
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
            'Criminal Petition For Leave to Appeal\.?\s*[\d&\/\-\s]+ of \d{4}' .
            'Criminal Petition For Leave to Appeal\?\s*[\d&\/\-\s]+ of \d{4}' .
            ')\b/i';

        if (preg_match($pattern, $clean, $match)) {
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
    /* protected function extractContentFromJudgment(string $text): ?string
    {
        if (preg_match('/\bJudgment\b[\s:]*\n*(.+)/is', $text, $matches)) {
            return trim($matches[1]);
        }
        return null;
    } */

    /* protected function extractContentFromJudgment(string $text): ?string
    {
        // Match exactly <br>Judgment<br> and capture everything after it (with HTML intact)
        if (preg_match('/<br>\s*Judgment\s*<br>(.*)/is', $text, $matches)) {
            return $matches[1]; // return raw HTML (with tags preserved)
        }
        return null;
    } */


    protected function extractContentFromJudgment(string $text): ?string
    {
        // 1) Normalize <br> variants and collapse extra whitespace around them
        $html = preg_replace('/\s*<br\s*\/?>\s*/i', '<br>', $text);

        // 2) Split exactly at "<br>Judgment<br>"
        $parts = preg_split('/<br>\s*Judgment\s*:?\s*<br>/i', $html, 2);
        if (!isset($parts[1])) {
            return null; // no Judgment anchor found
        }
        $after = $parts[1];

        // OPTIONAL: If you want to stop at a known next heading, uncomment:
        // $after = preg_split('/(<br>)\s*(Order|Result|Conclusion)\b/i', $after, 2)[0] ?? $after;

        // 3) Re-paragraph using heuristics
        //    - Start a new paragraph when a line looks like "2. ...", or starts with a year/citation-ish chunk
        //      like "20BLD(HCD)(2000)".
        $lines = preg_split('/<br>/i', $after);

        $paras = [];
        $buf = '';

        foreach ($lines as $idx => $rawLine) {
            $line = trim($rawLine);
            if ($line === '') {
                continue;
            }

            $isFirst = ($buf === '');
            $startsNumbered = (bool) preg_match('/^\d+\.\s/', $line);
            $startsCitation  = (bool) preg_match('/^(?:[12][0-9]{3}\b|\d{2,}[A-Za-z])/', $line); // e.g., 20BLD..., 1999...

            if (!$isFirst && ($startsNumbered || $startsCitation)) {
                // flush previous paragraph, start a new one
                $paras[] = $buf;
                $buf = $line;
            } else {
                // continue current paragraph
                $buf = $isFirst ? $line : ($buf . '<br>' . $line);
            }
        }
        if ($buf !== '') {
            $paras[] = $buf;
        }

        // 4) Build HTML with <p>…</p>
        $out = '<p>' . implode('</p><p>', $paras) . '</p>';

        // 5) Cleanup accidental empty paragraphs (just in case)
        $out = preg_replace('/<p>\s*<\/p>/', '', $out);

        return $out;
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


    // public function extractJudgmentDate(string $text): array
    // {
    //     $text = preg_replace("/\r\n|\r/", "\n", $text);
    //     if (preg_match('/(?:Date of Judgment|Decided On)\s*:?\s*((?:.|\n){0,100}?\b\d{4})/i', $text, $match)) {
    //         $rawDateText = preg_replace('/-\s*\n\s*/', '', $match[1]);
    //         $rawDateText = preg_replace('/\s+/', ' ', $rawDateText);
    //         $result = $this->extractFullDateAndMonth($rawDateText);
    //         dd($rawDateText);
    //         return $result ?? ['date' => null, 'month' => null];
    //     }

    //     return ['date' => null, 'month' => null];
    // }


    /* public function extractJudgmentDate(string $text): array
    {
        $text = preg_replace("/\r\n|\r/", "\n", $text);
        $lines = explode("\n", $text);

        foreach ($lines as $index => $line) {
            if (preg_match('/Date of Judgment|Decided On/i', $line)) {

                // Include nearby lines
                $combined = $line;
                if (isset($lines[$index + 1])) $combined .= ' ' . $lines[$index + 1];
                if (isset($lines[$index + 2])) $combined .= ' ' . $lines[$index + 2];
                if (isset($lines[$index - 1])) $combined = $lines[$index - 1] . ' ' . $combined;

                $combined = preg_replace('/\s+/', ' ', $combined);
                $result = $this->extractFullDateAndMonth($combined);

                return $result ?? ['date' => null, 'month' => null];
            }
        }

        return ['date' => null, 'month' => null];
    } */

    public function extractJudgmentDate(string $html): array
    {
        // Step 1: Normalize HTML to lines
        $text = preg_replace('/<br\s*\/?>|<\/p>|<p>/i', "\n", $html);
        $text = preg_replace("/\r\n|\r/", "\n", $text);

        // Step 2: Collapse multiple newlines
        $text = preg_replace("/\n+/", "\n", $text);

        $lines = explode("\n", $text);

        foreach ($lines as $index => $line) {
            if (preg_match('/Date of Judgment|Decided On/i', $line)) {

                // Step 3: Get nearby lines (3 before + 3 after)
                $start = max(0, $index - 3);
                $end = min(count($lines) - 1, $index + 3);
                $snippet = implode(' ', array_slice($lines, $start, $end - $start + 1));

                // Step 4: Clean extra spaces
                $snippet = preg_replace('/\s+/', ' ', $snippet);

                // Step 5: Extract full date using your existing helper
                $result = $this->extractFullDateAndMonth($snippet);

                return $result ?? ['date' => null, 'month' => null];
            }
        }

        return ['date' => null, 'month' => null];
    }




    public function extractFullDateAndMonth(string $text): ?array
    {
        $text = preg_replace('/\s+/', ' ', $text);

        $patterns = [

            // 1. DD-MM-YYYY or DD/MM/YYYY
            '/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/i' => function ($m) {
                $day = (int) $m[1];
                $month = (int) $m[2];
                $year = (int) $m[3];
                $monthName = date('F', mktime(0, 0, 0, $month, 10));
                return ['date' => "The " . $this->ordinalSuffix($day) . " {$monthName}, {$year}", 'month' => $monthName];
            },

            // 2. The 8th March 1997 (or without The)
            '/(?:the\s+)?(\d{1,2})(?:st|nd|rd|th)?\s+(January|February|March|April|May|June|July|August|September|October|November|December)\s*,?\s+(\d{4})/i' => function ($m) {
                $day = (int) $m[1];
                $month = ucfirst(strtolower($m[2]));
                $year = (int) $m[3];
                return ['date' => "The " . $this->ordinalSuffix($day) . " {$month}, {$year}", 'month' => $month];
            },

            // 3. March 8th, 1997
            '/(January|February|March|April|May|June|July|August|September|October|November|December)\s+(\d{1,2})(?:st|nd|rd|th)?,?\s+(\d{4})/i' => function ($m) {
                $month = ucfirst(strtolower($m[1]));
                $day = (int) $m[2];
                $year = (int) $m[3];
                return ['date' => "The " . $this->ordinalSuffix($day) . " {$month}, {$year}", 'month' => $month];
            },

            // 4. Split format: "of June", then "The 8th 1997"
            '/of\s+(January|February|March|April|May|June|July|August|September|October|November|December)/i' => function ($m) use ($text) {
                if (preg_match('/The\s+(\d{1,2})(?:st|nd|rd|th)?\s+(\d{4})/i', $text, $dy)) {
                    $day = (int) $dy[1];
                    $year = (int) $dy[2];
                    $month = ucfirst(strtolower($m[1]));
                    return ['date' => "The " . $this->ordinalSuffix($day) . " {$month}, {$year}", 'month' => $month];
                }
                return null;
            },
        ];

        foreach ($patterns as $regex => $handler) {
            if (preg_match($regex, $text, $matches)) {
                return $handler($matches);
            }
        }

        return null;
    }

    /**
     * Add ordinal suffix to a day number: 1 -> 1st, 2 -> 2nd, 3 -> 3rd, 4 -> 4th ...
     */
    protected function ordinalSuffix(int $number): string
    {
        $j = $number % 10;
        $k = $number % 100;

        if ($j == 1 && $k != 11) {
            return $number . "st";
        }
        if ($j == 2 && $k != 12) {
            return $number . "nd";
        }
        if ($j == 3 && $k != 13) {
            return $number . "rd";
        }
        return $number . "th";
    }





    // public function extractJudgmentDate(string $text): array
    // {
    //     // Normalize line breaks
    //     $text = preg_replace("/\r\n|\r/", "\n", $text);
    //     // Match "Date of Judgment" or "Decided On", capture up to 3 lines (date parts)
    //     if (preg_match('/(?:Date of Judgment|Decided On)\s*:?[\s\n]*((?:.*\n?){1,3})/i', $text, $match)) {
    //         $rawDateText = $match[1];

    //         // Remove unwanted line breaks, excess spaces, and extra words
    //         $rawDateText = str_replace(['-', "\n", "\r"], ' ', $rawDateText);
    //         $rawDateText = preg_replace('/\s+/', ' ', $rawDateText);
    //         $rawDateText = trim($rawDateText);
    //         $rawDateText = str_ireplace(['The', ','], '', $rawDateText); // remove "The" and comma

    //         // dd($rawDateText);
    //         // Now extract date and month
    //         return $this->extractFullDateAndMonth($rawDateText) ?? ['date' => null, 'month' => null];
    //     }

    //     return ['date' => null, 'month' => null];
    // }



    // public function extractFullDateAndMonth(string $text): ?array
    // {
    //     $text = preg_replace('/Result/i', '', $text);
    //     $text = preg_replace('/-\s*\n\s*/', '', $text);
    //     $validMonths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    //     $text = preg_replace_callback('/([A-Za-z]{3,})\s+([A-Za-z]+)/', function ($matches) use ($validMonths) {
    //         $joined = $matches[1] . $matches[2];
    //         foreach ($validMonths as $month) {
    //             if (stripos($month, $joined) === 0) {
    //                 return $joined; // Join broken month name
    //             }
    //         }
    //         return $matches[0]; // leave as is
    //     }, $text);

    //     // Step 4: Now match the full date
    //     /* if (preg_match('/The\s+(\d{1,2})(?:st|nd|rd|th)?\s+of\s+([A-Za-z]+),?\s*(\d{4})/i', $text, $matches)) {
    //         $fullDate = "The {$matches[1]} of {$matches[2]}, {$matches[3]}";
    //         $month = ucfirst(strtolower($matches[2]));
    //         return ['date' => $fullDate, 'month' => $month];
    //     } */
    //     if (preg_match('/The\s+(\d{1,2})(?:st|nd|rd|th)?\s+(?:of\s+)?([A-Za-z]+),?\s*(\d{4})?/i', $text, $matches)) {
    //         $day = $matches[1];
    //         $month = ucfirst(strtolower($matches[2]));
    //         $year = isset($matches[3]) ? $matches[3] : null;

    //         if ($year) {
    //             $fullDate = "The {$day} of {$month}, {$year}";
    //         } else {
    //             $fullDate = "The {$day} of {$month}";
    //         }

    //         return ['date' => $fullDate, 'month' => $month];
    //     }


    //     return ['date' => null, 'month' => null];
    // }




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

    /* public function extractLegalSections(string $text): ?string
    {
        // Match common formats like: Section 302, Sec. 144, Article 102, etc.
        // preg_match_all('/\b(?:section|sec\.?|article)\s+[\dA-Za-z\-]+/i', $text, $matches);
        preg_match_all('/\b(?:section|sec\.?)\s+[\dA-Za-z\-]+/i', $text, $matches);

        if (!empty($matches[0])) {
            $unique = array_unique(array_map('trim', $matches[0]));
            return implode(', ', $unique);
        }

        return null;
    } */

    /* public function extractLegalSections(string $text): ?string
    {
        // Match "Section:" or "Sub Section:" (case insensitive) with their values
        preg_match_all('/\b(Sub\s+Section|Section)\s*[:\-]\s*[A-Z0-9\- ]+/i', $text, $matches);

        if (!empty($matches[0])) {
            // Remove duplicates and join with comma
            $unique = array_unique(array_map('trim', $matches[0]));
            return implode(', ', $unique);
        }

        return null;
    } */

    public function extractLegalSections(string $text): ?string
    {
        // Match "Section" or "Sub Section" followed by colon or dash,
        // then capture only the first alphanumeric/dash group after that
        preg_match_all('/\b(Sub\s+Section|Section)\s*[:\-]\s*([A-Z0-9\-]+)/i', $text, $matches, PREG_SET_ORDER);

        if (!empty($matches)) {
            $results = [];

            foreach ($matches as $match) {
                $key = ucwords(strtolower($match[1])); // Section or Sub Section
                $value = strtoupper(trim($match[2]));  // section number/code in uppercase for consistency

                $results[] = "{$key}: {$value}";
            }

            $unique = array_unique($results);
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
