<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CareerRequest extends FormRequest
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
        return [
            'title'            => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255|unique:careers,slug,' . $this->route('career'),
            'department'       => 'nullable|string|max:255',
            'job_type'         => 'required|in:full-time,part-time,internship,contract',
            'job_level'        => 'nullable|in:entry,mid,senior,manager',
            'vacancy'          => 'nullable|integer|min:0',
            'description'      => 'required|string',
            'responsibilities' => 'nullable|string',
            'requirements'     => 'nullable|string',
            'education'        => 'nullable|string',
            'location'         => 'nullable|string|max:255',
            'salary'           => 'nullable|string|max:255',
            'apply_email'      => 'nullable|email|max:255',
            'apply_url'        => 'nullable|url|max:255',
            'deadline'         => 'nullable|date',
            'published_at'     => 'nullable|date',
            'job_status'       => 'required|in:published,unpublished',
            'status'           => 'required|in:0,1',
        ];
    }
}
