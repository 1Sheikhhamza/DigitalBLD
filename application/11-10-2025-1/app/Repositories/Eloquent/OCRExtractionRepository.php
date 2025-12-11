<?php

namespace App\Repositories\Eloquent;

use App\Models\OCRExtraction;
use App\Models\Volume;
use App\Repositories\Contracts\OCRExtractionRepositoryInterface;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OCRExtractionRepository implements OCRExtractionRepositoryInterface
{
    ////////////////// Admin ////////
    /* public function index()
    {
        return OCRExtraction::with('volume:id,number,year,index_file')->orderBy('id', 'desc')->paginate(50);
    } */

    public function index($filters = [])
    {
        $query = OCRExtraction::with('volume:id,number,year,index_file')
            ->orderBy('id', 'desc');

        if (!empty($filters['case_no'])) {
            $query->where('case_no', 'LIKE', '%' . $filters['case_no'] . '%');
        }

        if (!empty($filters['parties'])) {
            $query->where('parties', 'LIKE', '%' . $filters['parties'] . '%');
        }

        if (!empty($filters['division'])) {
            $query->where('division', $filters['division']);
        }

        if (!empty($filters['volume_id'])) {
            $query->where('volume_id', $filters['volume_id']);
        }

        if (!empty($filters['published_year'])) {
            $query->whereYear('decided_on', $filters['published_year']);
        }

        if (!empty($filters['judgename'])) {
            $query->where('judgename', 'LIKE', '%' . $filters['judgename'] . '%');
        }

        return $query->paginate(50)->appends($filters);
    }



    public function create(array $data)
    {
        // dd($data);
        $volumeData = [
            'number' => $data['volume_id'],
            'year' => $data['published_year'],
            'index_file' => $data['index_file'] ?? null,
            'status' => 1,
        ];
        $volume_id = $this->volumeInsert($volumeData);
        return OCRExtraction::create([
            'volume_id'             => $volume_id,
            'book_volume'           => $data['book_volume'] ?? null,
            'published_year'        => $data['published_year'] ?? null,
            'decided_on'            => $data['decided_on'] ?? null,
            'published_month'       => $this->getJudgmentMonth($data['decided_on']) ?? null,
            'starting_page_no'      => $data['starting_page_no'] ?? null,
            'ending_page_no'        => $data['ending_page_no'] ?? null,
            'division'              => $data['division'] ?? null,
            'judge_name'            => $data['judge_name'] ?? null,
            'parties'               => $data['parties'] ?? null,
            'petitioners'           => $data['petitioners'] ?? null,
            'respondent'            => $data['respondent'] ?? null,
            'related_act_order_rule' => $data['related_act_order_rule'] ?? null,
            'sections_subsections'  => $data['sections_subsections'] ?? null,
            'key_words'             => $data['key_words'] ?? null,
            'subject'               => $data['subject'] ?? null,
            'result'               => $data['result'] ?? null,
            'case_no'               => $data['case_no'] ?? null,
            'jurisdiction'          => $data['jurisdiction'] ?? null,
            'judgment'              => $data['judgment'] ?? null,
            'created_at'            => now(),
            'updated_at'            => now(),
        ]);
    }

    private function getJudgmentMonth($inputDate)
    {
        $clean = preg_replace('/^The\s+/i', '', $inputDate);
        $clean = preg_replace('/(\d+)(st|nd|rd|th)/i', '$1', $clean);
        $date = DateTime::createFromFormat('j F, Y', trim($clean));

        if ($date !== false) {
            return $date->format('F');
        }

        return null; // Invalid date
    }





    public function volumeInsert(array $volumeData)
    {
        // Check by volume number only
        $volume = Volume::where('number', $volumeData['number'])->first();

        $updateData = [
            'number'     => $volumeData['number'] ?? ($volume ? $volume->number : null),
            'year'       => $volumeData['year'] ?? ($volume ? $volume->year : null),
            'index_file' => $volumeData['index_file'] ?? ($volume ? $volume->index_file : null),
            'status'     => $volumeData['status'] ?? 1,
        ];

        if ($volume) {
            // Update existing record
            $volume->update($updateData);
            return $volume->id;
        }

        // Create a new one
        $newVolume = Volume::create($updateData);

        return $newVolume->id;
    }



    public function find($id)
    {
        return OCRExtraction::with('volume:id,number,year,index_file')->find($id);
    }




    public function update($id, array $data)
    {
        $currentData = OCRExtraction::findOrFail($id);

        $volumeData = [
            'number' => $data['volume_id'],
            'year' => $data['published_year'],
            'index_file' => $data['index_file'] ?? null,
            'status' => 1,
        ];
        $volume_id = $this->volumeInsert($volumeData);

        $menuUpdate = [
            'volume_id'             => $data['volume_id'] ? $volume_id : $currentData->volume_id,
            // 'book_volume'           => $data['book_volume'] ?? $currentData->book_volume,
            'published_year'        => $data['published_year'] ?? $currentData->published_year,
            'decided_on'            => $data['decided_on'] ?? $currentData->decided_on,
            'published_month'       => $this->getJudgmentMonth($data['decided_on']) ?? $currentData->published_month,
            'starting_page_no'      => $data['starting_page_no'] ?? $currentData->starting_page_no,
            'ending_page_no'        => $data['ending_page_no'] ?? $currentData->ending_page_no,
            'division'              => $data['division'] ?? $currentData->division,
            'judge_name'            => $data['judge_name'] ?? $currentData->judge_name,
            'parties'               => $data['parties'] ?? $currentData->parties,
            'petitioners'           => $data['petitioners'] ?? $currentData->petitioners,
            'respondent'            => $data['respondent'] ?? $currentData->respondent,
            'related_act_order_rule' => $data['related_act_order_rule'] ?? $currentData->related_act_order_rule,
            'sections_subsections'  => $data['sections_subsections'] ?? $currentData->sections_subsections,
            'key_words'             => $data['key_words'] ?? $currentData->key_words,
            'subject'               => $data['subject'] ?? $currentData->subject,
            'result'               => $data['result'] ?? $currentData->result,
            'case_no'               => $data['case_no'] ?? $currentData->case_no,
            'jurisdiction'          => $data['jurisdiction'] ?? $currentData->jurisdiction,
            'judgment'              => $data['judgment'] ?? $currentData->judgment,
            'updated_at'            => now(),
        ];

        $currentData->update($menuUpdate);

        return true;
    }




    public function delete($id)
    {
        $patientData = OCRExtraction::find($id);

        if (!$patientData) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        return $patientData->delete(); // Soft delete the record
    }



    public function restore($id)
    {
        $patientData = OCRExtraction::onlyTrashed()->find($id);

        if (!$patientData) {
            return response()->json(['message' => 'No deleted record found'], 404);
        }

        $patientData->restore(); // Restore the record

        return response()->json(['message' => 'OCRExtraction member restored successfully', 'data' => $patientData]);
    }

    public function forceDelete($id)
    {
        $patientData = OCRExtraction::onlyTrashed()->find($id);

        if (!$patientData) {
            return response()->json(['message' => 'No deleted record found'], 404);
        }

        $patientData->forceDelete(); // Permanently delete the record

        return response()->json(['message' => 'OCRExtraction member permanently deleted']);
    }
}
