<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OCRExtractionRequest extends FormRequest
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
        $startYear = date('Y') - 49;  // 50 years including current year
        $currentYear = date('Y');
        $years = range($startYear, $currentYear);

        return [
            'volume_id' => 'required|numeric',
            'year'      => 'required|numeric|in:' . implode(',', $years),
            // 'month'     => 'required|numeric|in:1,2,3,4,5,6,7,8,9,10,11,12',
            // 'content'   => 'nullable|string',
            'document' => 'required|file|mimes:pdf,docx,jpg,jpeg,png|max:102400',
        ];
    }
}
