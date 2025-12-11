<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        return [
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'phone_no'      => 'required|unique:users,mobile',
            'email'         => 'required|email|unique:users,email',
            'date_of_birth' => 'required|date',
            'gender'        => 'required|in:Male,Female,Other,Not Declare',
            'password'      => 'required|min:6',
        ];
    }
}
