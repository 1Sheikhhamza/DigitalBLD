<?php

namespace App\Console\Commands;

use App\Models\OCRExtraction;
use App\Models\Volume;
use Illuminate\Console\Command;

class OrganizeScobVolumes extends Command
{
    protected $signature = 'scob:organize-volumes';
    protected $description = 'Organize SCOB decisions into year-based volumes';

    public function handle()
    {
        $this->info('Starting SCOB volume organization...');

        // Delete existing SCOB 2024 volume if it exists
        $existingVolume = Volume::where('volume_type', 'SCOB')->where('number', 2024)->first();
        if ($existingVolume) {
            $this->info("Removing temporary volume {$existingVolume->id}...");
            OCRExtraction::where('volume_id', $existingVolume->id)->update(['volume_id' => null]);
            $existingVolume->delete();
        }

        // Step 1: Extract unique years using chunking
        $this->info("Step 1: Extracting years from case numbers...");
        $yearCounts = [];

        OCRExtraction::where('file_path', 'LIKE', '%scob%')
            ->whereNotNull('case_no')
            ->select('case_no')
            ->chunk(500, function ($decisions) use (&$yearCounts) {
                foreach ($decisions as $decision) {
                    if (preg_match('/\/(\d{4})/', $decision->case_no, $matches)) {
                        $year = (int) $matches[1];
                        if ($year >= 1950 && $year <= 2030) {
                            $yearCounts[$year] = ($yearCounts[$year] ?? 0) + 1;
                        }
                    }
                }
            });

        ksort($yearCounts);
        $this->info("Found decisions across " . count($yearCounts) . " years");

        // Step 2: Create volumes for each year
        $this->info("Step 2: Creating volumes...");
        $volumeMap = [];
        foreach ($yearCounts as $year => $count) {
            $volume = Volume::create([
                'number' => $year,
                'year' => $year,
                'volume_type' => 'SCOB',
                'status' => 1,
            ]);

            $volumeMap[$year] = $volume->id;
            $this->info("  Volume {$year} (ID: {$volume->id}) - {$count} decisions");
        }

        // Step 3: Link decisions to volumes using chunking
        $this->info("Step 3: Linking decisions to volumes...");
        $linked = 0;

        OCRExtraction::where('file_path', 'LIKE', '%scob%')
            ->whereNotNull('case_no')
            ->chunk(500, function ($decisions) use (&$linked, $volumeMap) {
                foreach ($decisions as $decision) {
                    if (preg_match('/\/(\d{4})/', $decision->case_no, $matches)) {
                        $year = (int) $matches[1];
                        if (isset($volumeMap[$year])) {
                            $decision->update(['volume_id' => $volumeMap[$year]]);
                            $linked++;
                        }
                    }
                }
                $this->info("  Processed {$linked} decisions so far...");
            });

        $this->info("Successfully linked {$linked} decisions to year-based volumes!");
        $this->info('SCOB volume organization complete.');

        return 0;
    }
}
