<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VolumeRequest extends FormRequest
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
    public function rules()
    {
        $startYear = date('Y') - 49;  // 50 years including current year
        $currentYear = date('Y');
        $years = range($startYear, $currentYear);
        
        return [
            // 'number' => 'required|numeric|unique:volumes,number,' . $this->route('volume'),
            'number' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'year'      => 'required|numeric|in:' . implode(',', $years),
            // 'index_file' => 'nullable|mimes:pdf|max:10240',
            'index_file' => 'nullable|mimes:pdf|max:51200',
        ];
    }
}
