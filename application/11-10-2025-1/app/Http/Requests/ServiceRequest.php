<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
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
            'name'             => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255|unique:services,slug,' . $this->route('service'),
            'description'      => 'nullable|string',
            'icon'             => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'banner'            => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords'    => 'nullable|string|max:255',
        ];
    }
}
