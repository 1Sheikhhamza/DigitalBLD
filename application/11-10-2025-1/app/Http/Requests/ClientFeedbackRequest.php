<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientFeedbackRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_name'     => 'required|string|max:255',
            'client_position' => 'nullable|string|max:255',
            'feedback'        => 'required|string',
            'client_photo'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'rating'          => 'nullable|integer|min:1|max:5',
            'company'         => 'nullable|string|max:255',
            'website'         => 'nullable|url|max:255',
            'status'          => 'nullable|in:0,1',
        ];
    }
}
