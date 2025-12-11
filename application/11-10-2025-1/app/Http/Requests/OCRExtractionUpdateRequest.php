<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OCRExtractionUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // must be true to allow validation to run
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $startYear = date('Y') - 100;  // Last 50 years including current year
        $currentYear = date('Y');
        $years = range($startYear, $currentYear);

        return [
            'volume_id'             => 'required|numeric',
            'book_volume'           => 'nullable|string',
            'published_year'        => 'nullable|numeric|in:' . implode(',', $years),
            'decided_on' => 'nullable|date_format:"\T\h\e jS F, Y"',
            'published_month'       => 'nullable',
            'starting_page_no'      => 'nullable|numeric',
            'ending_page_no'        => 'nullable|numeric',
            'division'              => 'nullable|string',
            'judge_name'            => 'nullable|string',
            'judges'                => 'nullable|string',
            'parties'               => 'nullable|string',
            'petitioners'           => 'nullable|string',
            'respondent'            => 'nullable|string',
            'related_act_order_rule' => 'nullable|string',
            'sections_subsections'  => 'nullable|string',
            'key_words'             => 'nullable|string',
            'subject'               => 'nullable|string',
            'result'               => 'nullable|string',
            'case_no'               => 'nullable|string',
            'jurisdiction'          => 'nullable|string',
            'judgment'              => 'nullable|string',
            'content'               => 'nullable|string',
            'index_file'            => 'nullable|mimes:pdf|max:51200',
            // 'document'              => 'nullable|file|mimes:pdf,docx,jpg,jpeg,png|max:102400',
        ];
    }

    public function messages(): array
    {
        return [
            'decided_on.date_format' => 'The Date of Judgment must be in the format: "The 8th June, 1997".',
        ];
    }
}
