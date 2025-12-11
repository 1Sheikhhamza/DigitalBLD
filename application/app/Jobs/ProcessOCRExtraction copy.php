<?php

namespace App\Jobs;

use App\Models\OCRExtraction;
use App\Models\Volume;
use App\Services\OCRExtractionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\OcrResultMail;

class ProcessOCRExtraction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $volume_id;
    public int $volume_year;
    public $volume_number;
    public string $filePath;
    public string $extension;
    public string $resultMessage;

    /**
     * Allow this job to run up to 30 minutes.
     */
    public int $timeout = 3600;

    public function __construct(int $volume_id, int $volume_year, $volume_number, string $filePath, string $extension, string $resultMessage)
    {
        $this->volume_id = $volume_id;
        $this->volume_year = $volume_year;
        $this->volume_number = $volume_number;
        $this->filePath = $filePath;
        $this->extension = $extension;
        $this->resultMessage = $resultMessage;
    }

    public function handle(OCRExtractionService $service): void
    {
        Log::info("Starting OCR Job for path: {$this->filePath}");

        try {
            // $text = $service->extractText(storage_path('app/' . $this->filePath), $this->extension);
            // dd($text);
            // Log::info("API Data:", ['text' => $text]);
            // For test porpuse
            $text = DB::table('ocr_extractions')->where('id', 104)->value('content');
            $text = preg_replace('/([A-Za-z])-\s+([A-Za-z])/', '$1$2', $text);
            $sections = $service->splitLegalText($text, ['Appellate Division', 'High Court Division']);
            // For single row testing
            // $section = $sections[1];
            // $index = 0;
            foreach ($sections as $index => $section) {
                $extracted = $service->extractDataFromLegalText($section['content'], $section['title'], $this->volume_year, $this->volume_number);
                $partySplit = $service->splitParties($extracted['parties'] ?? '');
                $pages = $service->extractPageNumbers($section['content']);
                
                if (isset($sections[$index + 1])) {
                    $nextSectionPages = $service->extractPageNumbers($sections[$index + 1]['content']);
                    if ($nextSectionPages['start']) {
                        $pages['end'] = $nextSectionPages['start'] - 1;
                    }
                }
                
                // Keyword filtering
                $englishStopWords = [
                    'the', 'and', 'is', 'was', 'are', 'of', 'to', 'in', 'with', 'that',
                    'for', 'on', 'by', 'as', 'at', 'be', 'this', 'from', 'or', 'an',
                    'it', 'has', 'have', 'a', 'not', 'but', 'were', 'will', 'shall',
                    'can', 'may', 'might', 'should', 'would', 'been', 'being', 'into',
                    'such', 'there', 'here', 'where', 'which', 'who', 'whom'
                ];
                
                $nameTitles = [
                    'mr', 'mst', 'md', 'mrs', 'miss', 'ms', 'dr', 'advocate',
                    'barrister', 'professor', 'justice', 'j', 'jj', 'esq'
                ];
                
                // 🔥 Extended Bengali/common South Asian names
                $commonBengaliNames = [
                    'rahman', 'hasan', 'hossain', 'khan', 'chowdhury', 'islam',
                    'ali', 'ahmed', 'uddin', 'begum', 'kabir', 'haque', 'miah',
                    'rahim', 'karim', 'mostafa', 'shah', 'siddique', 'uddin',
                    'aziz', 'salam', 'rafiq', 'mahbub', 'alam', 'nazrul', 'bashar',
                    'faisal', 'faruk', 'akram', 'sadia', 'jasmin', 'nahar',
                    'huda', 'latif', 'atif', 'babul', 'sohel', 'asif'
                ];
                
                // 🆕 Custom domain-specific legal/case keywords to exclude
                $legalStopWords = [
                    'respondent', 'respondents', 'petitioner', 'petitioners',
                    'appellant', 'appellants', 'state', 'condemned', 'defd-appellant',
                    'opposite', 'opposite party', 'opposite parties',
                    'defendant', 'defendant opposite part', 'plaintiff', 'plaintiffs',
                    'plaintiff-respondents', 'accused', 'applicant',
                    'writ', 'petition', 'case', 'bld', 'bangladesh', 'issued',
                    'jurisdiction', 'under', 'section', 'sub section', 'sub-section',
                    'order', 'rule', 'appeal', 'appeals', 'court', 'tribunal',
                    'bench', 'division', 'judge', 'justices', 'law', 'act', 'code',
                    'provision', 'application', 'matter', 'civil', 'criminal',
                    'revision', 'suit', 'trial', 'judgment', 'verdict', 'motion',
                    'cause', 'hearing', 'counsel', 'advocate', 'bar', 'legal',
                    'authority'
                ];
                
                // Build human name words from extracted data (judge names + parties)
                $humanNamesWords = array_filter(array_unique(
                    preg_split('/\s+/', strtolower($extracted['judgename'] . ' ' . $extracted['parties']))
                ));
                
                // Final stopwords list (unique merged)
                $stopwords = array_unique(array_merge(
                    $englishStopWords,
                    $nameTitles,
                    $commonBengaliNames,
                    $legalStopWords,
                    $humanNamesWords
                ));
                
                $keywords = $service->extractCaseKeywords($extracted['content'] ?? '', $stopwords, 20);
                $sectionsValue = $service->extractLegalSections($section['content']);
                $judgmentDate = $service->extractJudgmentDate($section['content']);
                
                dd($extracted);
                
                DB::table('ocr_extractions')->insert([
                    'volume_id' => $this->volume_id,
                    'published_year' => $this->volume_year,
                    // 'book_volume' => $service->extractVolume($section['content']),
                    'book_volume' => $this->volume_number,
                    // 'published_year' => $service->extractPublishedYear($section['content']),
                    'decided_on' => $judgmentDate['date'],
                    'published_month' => $judgmentDate['month'],
                    'starting_page_no' => $pages['start'],
                    'ending_page_no' => $pages['end'],
                    // 'division' => $section['title'] ?? null,
                    'division' => $extracted['division'] ?? null,
                    'judge_name' => $extracted['judgename'] ?? null,
                    'judges' => $extracted['judgename'] ?? null,
                    'parties' => $extracted['parties'] ?? null,
                    'petitioners' => $extracted['extractPetitionersAndRespondents']['petitioners'] ? join('<br/>',$extracted['extractPetitionersAndRespondents']['petitioners']) : null,
                    'respondent' => $extracted['extractPetitionersAndRespondents']['respondents'] ? join('<br/>',$extracted['extractPetitionersAndRespondents']['respondents']) : null,
                    'related_act_order_rule' => $extracted['ref_law'] ?? null,
                    'sections_subsections' => $sectionsValue,
                    'key_words' => implode(', ', $keywords),
                    'subject' => $extracted['case_type'] ?? null,
                    'case_no' => $extracted['petition_no'] ?? null,
                    'jurisdiction' => $extracted['jurisdiction'] ?? null,
                    'judgment' => $extracted['content'] ?? null,
                    'content' => $text,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }

            Log::info("OCR job completed and inserted for path: {$this->filePath}");
            $volume = Volume::find($this->volume_id);
            if ($volume) {
                Mail::to(config('mail.ocr_mail')) // or any email property you want
                    ->queue(new OcrResultMail(
                        success: true,
                        message: $this->resultMessage,
                        volumeId: $this->volume_id
                    ));
            }
        } catch (\Throwable $e) {
            Log::error("OCR Job Failed [Path: {$this->filePath}]: " . $e->getMessage());
            Log::debug("Stack Trace: " . $e->getTraceAsString());

            // Optionally rethrow to trigger failed() handler
            throw $e;
        }
    }



    /* public function handle(OCRExtractionService $service): void
    {
        Log::info("Starting OCR Job for path: {$this->filePath}");

        try {
            $textOrPages = $service->extractText(storage_path('app/' . $this->filePath), $this->extension);
            dd($textOrPages);
            if (is_array($textOrPages)) {
                // OCR returned pages as array [pageNumber => text]
                foreach ($textOrPages as $pageNumber => $pageText) {
                    $sections = $service->splitLegalText($pageText, ['Appellate Division', 'High Court Division']);

                    foreach ($sections as $index => $section) {
                        $extracted = $service->extractDataFromLegalText($section['content'], $section['title']);
                        $partySplit = $service->splitParties($extracted['parties'] ?? '');
                        $pages = $service->extractPageNumbers($section['content']);

                        // Since this is per page, override start/end pages
                        $pages['start'] = $pageNumber;
                        $pages['end'] = $pageNumber;

                        // Optional: check next section pages if needed

                        $englishStopWords = ['the', 'and', 'is', 'was', 'are', 'of', 'to', 'in', 'with', 'that', 'for', 'on', 'by', 'as', 'at', 'be', 'this', 'from', 'or', 'an', 'it', 'has', 'have', 'a', 'not', 'but', 'were'];
                        $nameTitles = ['mr', 'mst', 'md', 'mrs', 'miss', 'ms', 'dr', 'advocate', 'barrister'];
                        $commonBengaliNames = ['rahman', 'hasan', 'hossain', 'khan', 'chowdhury', 'islam', 'ali', 'ahmed', 'uddin', 'begum', 'kabir', 'haque', 'miah', 'rahim', 'karim', 'mostafa', 'shah', 'siddique'];
                        $humanNamesWords = array_filter(array_unique(preg_split('/\s+/', strtolower($extracted['judgename'] . ' ' . $extracted['parties']))));
                        $stopwords = array_unique(array_merge($englishStopWords, $nameTitles, $commonBengaliNames, $humanNamesWords));

                        $keywords = $service->extractCaseKeywords($extracted['content'] ?? '', $stopwords, 10);
                        $sectionsValue = $service->extractLegalSections($section['content']);
                        $judgmentDate = $service->extractJudgmentDate($section['content']);

                        DB::table('ocr_extractions')->insert([
                            'volume_id' => 1,
                            'book_volume' => $service->extractVolume($section['content']),
                            'published_year' => $service->extractPublishedYear($section['content']),
                            'decided_on' => $judgmentDate['date'],
                            'published_month' => $judgmentDate['month'],
                            'starting_page_no' => $pages['start'],
                            'ending_page_no' => $pages['end'],
                            'division' => $extracted['division'] ?? null,
                            'judge_name' => $extracted['judgename'] ?? null,
                            'judges' => $extracted['judgename'] ?? null,
                            'parties' => $extracted['parties'] ?? null,
                            'petitioners' => $partySplit['petitioners'] ?? null,
                            'respondent' => $partySplit['respondent'] ?? null,
                            'related_act_order_rule' => $extracted['ref_law'] ?? null,
                            'sections_subsections' => $sectionsValue,
                            'key_words' => implode(', ', $keywords),
                            'subject' => $extracted['case_type'] ?? null,
                            'case_no' => $extracted['petition_no'] ?? null,
                            'jurisdiction' => $extracted['jurisdiction'] ?? null,
                            'judgment' => $extracted['content'] ?? null,
                            'content' => $pageText,  // content per page
                            'created_at' => now(),
                        ]);
                    }
                }
            } else {
                // Text is single string (non-PDF or PDF without OCR pages)
                $sections = $service->splitLegalText($textOrPages, ['Appellate Division', 'High Court Division']);

                foreach ($sections as $index => $section) {
                    $extracted = $service->extractDataFromLegalText($section['content'], $section['title']);
                    $partySplit = $service->splitParties($extracted['parties'] ?? '');
                    $pages = $service->extractPageNumbers($section['content']);

                    if (isset($sections[$index + 1])) {
                        $nextSectionPages = $service->extractPageNumbers($sections[$index + 1]['content']);
                        if ($nextSectionPages['start']) {
                            $pages['end'] = $nextSectionPages['start'] - 1;
                        }
                    }

                    $englishStopWords = ['the', 'and', 'is', 'was', 'are', 'of', 'to', 'in', 'with', 'that', 'for', 'on', 'by', 'as', 'at', 'be', 'this', 'from', 'or', 'an', 'it', 'has', 'have', 'a', 'not', 'but', 'were'];
                    $nameTitles = ['mr', 'mst', 'md', 'mrs', 'miss', 'ms', 'dr', 'advocate', 'barrister'];
                    $commonBengaliNames = ['rahman', 'hasan', 'hossain', 'khan', 'chowdhury', 'islam', 'ali', 'ahmed', 'uddin', 'begum', 'kabir', 'haque', 'miah', 'rahim', 'karim', 'mostafa', 'shah', 'siddique'];
                    $humanNamesWords = array_filter(array_unique(preg_split('/\s+/', strtolower($extracted['judgename'] . ' ' . $extracted['parties']))));
                    $stopwords = array_unique(array_merge($englishStopWords, $nameTitles, $commonBengaliNames, $humanNamesWords));

                    $keywords = $service->extractCaseKeywords($extracted['content'] ?? '', $stopwords, 10);
                    $sectionsValue = $service->extractLegalSections($section['content']);
                    $judgmentDate = $service->extractJudgmentDate($section['content']);

                    DB::table('ocr_extractions')->insert([
                        'volume_id' => 1,
                        'book_volume' => $service->extractVolume($section['content']),
                        'published_year' => $service->extractPublishedYear($section['content']),
                        'decided_on' => $judgmentDate['date'],
                        'published_month' => $judgmentDate['month'],
                        'starting_page_no' => $pages['start'],
                        'ending_page_no' => $pages['end'],
                        'division' => $extracted['division'] ?? null,
                        'judge_name' => $extracted['judgename'] ?? null,
                        'judges' => $extracted['judgename'] ?? null,
                        'parties' => $extracted['parties'] ?? null,
                        'petitioners' => $partySplit['petitioners'] ?? null,
                        'respondent' => $partySplit['respondent'] ?? null,
                        'related_act_order_rule' => $extracted['ref_law'] ?? null,
                        'sections_subsections' => $sectionsValue,
                        'key_words' => implode(', ', $keywords),
                        'subject' => $extracted['case_type'] ?? null,
                        'case_no' => $extracted['petition_no'] ?? null,
                        'jurisdiction' => $extracted['jurisdiction'] ?? null,
                        'judgment' => $extracted['content'] ?? null,
                        'content' => $textOrPages,
                        'created_at' => now(),
                    ]);
                }
            }

            Log::info("OCR job completed and inserted for path: {$this->filePath}");
        } catch (\Throwable $e) {
            Log::error("OCR Job Failed [Path: {$this->filePath}]: " . $e->getMessage());
            Log::debug("Stack Trace: " . $e->getTraceAsString());
            // Optionally rethrow or handle error
        }
    } */


    /**
     * Handle hard job failure (e.g., memory limit, timeout).
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical("OCR Job HARD FAILED for ID {$this->volume_id}: " . $exception->getMessage());

        Volume::where('id', $this->volume_id)->update([
            'status' => 0,
            'failed' => substr($exception->getMessage(), 0, 1000),
        ]);

        // Send failure mail
        $volume = Volume::find($this->volume_id);
        if ($volume) {
            Mail::to(config('mail.ocr_mail'))
                ->queue(new OcrResultMail(
                    success: false,
                    message: $exception->getMessage(),
                    volumeId: $this->volume_id
                ));
        }
    }
}
