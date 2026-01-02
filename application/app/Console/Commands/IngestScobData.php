<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OCRExtraction;
use Illuminate\Support\Facades\DB;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Str;

class IngestScobData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ingest:scob';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ingest SCOB judgments from CSV and PDFs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Increase memory limit for PDF parsing
        ini_set('memory_limit', '512M');

        $csvPath = '/Users/sheikhhamza/Desktop/Anti Gravatity/DigitalBLD_Launch_Ready/SCOB Judgements /judgment_mapping.csv';
        $basePdfDir = '/Users/sheikhhamza/Desktop/Anti Gravatity/DigitalBLD_Launch_Ready/SCOB Judgements /';

        // 1. Ensure public link exists for frontend access
        $publicLinkPath = public_path('storage/scob');
        if (!file_exists($publicLinkPath)) {
            $this->info("Creating symlink for public access...");
            if (!file_exists(public_path('storage'))) {
                mkdir(public_path('storage'), 0755, true);
            }
            try {
                symlink($basePdfDir, $publicLinkPath);
                $this->info("Symlink created: $publicLinkPath -> $basePdfDir");
            } catch (\Exception $e) {
                $this->error("Failed to create symlink: " . $e->getMessage());
            }
        }

        if (!file_exists($csvPath)) {
            $this->error("CSV file not found at: $csvPath");
            return 1;
        }

        $file = fopen($csvPath, 'r');
        // Get headers
        $header = fgetcsv($file);
        // Expected ID,Serial No,Case No,Upload Date,Parties,Decision,Local PDF Path,Original PDF Link

        $this->info("Starting ingestion...");

        $parser = new Parser();
        $count = 0;
        $skipped = 0;
        $errors = 0;

        // DB::beginTransaction(); // Removed transaction for individual error handling

        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($header, $row);

            // Fields
            $caseNo = $data['Case No'] ?? null;
            $parties = $data['Parties'] ?? null;
            $decision = $data['Decision'] ?? null;
            $uploadDate = $data['Upload Date'] ?? null;

            // 1. Clean Local PDF Path
            // The CSV contains "/Users/sheikhhamza/Desktop/Anti Gravatity/Data scrabing /pdfs/..."
            // But the actual file is likely in the current folder or needs to be mapped.
            // Based on User Request: "path: '/Users/sheikhhamza/Desktop/Anti Gravatity/DigitalBLD_Launch_Ready/SCOB Judgements '"
            // And CSV sample: "/Users/sheikhhamza/Desktop/Anti Gravatity/Data scrabing /pdfs/1_Writ_Petition_2706_2024_Writ_Petition_2706_2024_.pdf"
            // The filenames seem to match the directory listing we saw earlier (e.g. 1_Writ_Petition_...).
            // We just need the basename.

            $originalPdfPathInCsv = $data['Local PDF Path'] ?? '';
            $filename = basename($originalPdfPathInCsv);
            $realPdfPath = $basePdfDir . $filename;

            if (!$caseNo) {
                continue; // Skip invalid rows
            }

            // Check duplicate
            if (OCRExtraction::where('case_no', $caseNo)->exists()) {
                $this->warn("Skipping existing case: $caseNo");
                $skipped++;
                continue;
            }

            // 2. Extract Text from PDF with better error handling
            $judgmentText = '';
            if (file_exists($realPdfPath)) {
                try {
                    // Force garbage collection before parsing
                    gc_collect_cycles();

                    $pdf = $parser->parseFile($realPdfPath);
                    $judgmentText = $pdf->getText();

                    // Free memory immediately after extraction
                    unset($pdf);
                    gc_collect_cycles();

                } catch (\Exception $e) {
                    $this->warn("Failed to parse PDF for $caseNo: " . $e->getMessage());
                    $errors++;
                    // Continue inserting metadata even if PDF fails, but record the error in text
                    $judgmentText = "Error extracting text from PDF: " . $e->getMessage();
                }
            } else {
                $this->warn("PDF file not found: $realPdfPath");
                $judgmentText = "PDF not found.";
            }

            // 3. Insert into DB
            try {
                OCRExtraction::create([
                    'case_no' => $caseNo,
                    'parties' => $parties,
                    'file_path' => 'storage/scob/' . $filename, // Public accessible path
                    'judgment' => mb_convert_encoding($judgmentText, 'UTF-8', 'UTF-8'), // Ensure UTF-8
                    'division' => 'SCOB',
                    'decided_on' => $uploadDate,
                    'subject' => $decision, // Mapping decision to subject for now, or could store elsewhere.
                    // Nullable fields
                    'volume_id' => null,
                    'book_volume' => null,
                    'published_year' => null, // Could parse from date if needed
                    'judges' => null, // Could extract from text potentially later
                ]);

                $count++;

                if ($count % 50 == 0) {
                    $this->info("Processed $count records... (Skipped: $skipped, Errors: $errors)");
                    // Force garbage collection periodically
                    gc_collect_cycles();
                }

            } catch (\Exception $e) {
                $this->error("Failed to insert record for $caseNo: " . $e->getMessage());
                $errors++;
            }
        }

        fclose($file);
        $this->info("Ingestion complete!");
        $this->info("Inserted: $count");
        $this->info("Skipped: $skipped");
        $this->info("Errors: $errors");

        return 0;
    }
}
