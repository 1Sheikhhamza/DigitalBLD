<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Must be true to allow validation to run
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $pageId = $this->route('page') ?? null;

        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pages', 'title')
                    ->ignore($pageId)   // ignore current page for update
                    ->whereNull('deleted_at') // ignore soft deleted records
            ],
            'parent_id'  => 'nullable|numeric',
            'content' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'page_structure' => 'required|in:Text,Page,URL',
            'connected_page' => 'nullable|exists:modules,name',
            'external_url' => 'nullable|url',
            'homepage_display' => 'nullable|in:0,1',
            'menu_type' => 'required|in:Main Menu,Footer Menu,Without Menu',
            'why_choose' => 'nullable|in:0,1',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
        ];
    }
}
