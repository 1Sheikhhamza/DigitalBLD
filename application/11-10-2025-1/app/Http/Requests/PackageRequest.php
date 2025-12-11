<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PackageRequest extends FormRequest
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
        $packageId = $this->route('package') ?? null;

        return [
            'name' => 'required|string|max:255|unique:packages,name,' . $packageId,
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'duration_type' => 'required|in:monthly,quarterly,half_yearly,yearly,lifetime',
            'duration_in_days' => 'required|integer',
            'status' => 'required|boolean',
            'is_featured' => 'nullable|boolean',
            'button_text' => 'nullable|string|max:255',
            'currency' => 'nullable|string|max:10',
            'highlight_badge' => 'nullable|string|max:50',
            'icon' => 'nullable|image|mimes:jpg,png,jpeg,gif,webp|max:2048',
            'features' => 'required|array',
            'features.*' => 'exists:package_features,id',
            'modules' => 'nullable|array',
            'modules.*' => 'exists:package_feature_modules,id',
        ];
    }
}
