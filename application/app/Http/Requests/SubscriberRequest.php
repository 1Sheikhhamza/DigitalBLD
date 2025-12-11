<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscriberRequest extends FormRequest
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
        $subscriberId = $this->route('subscriber') ?? null;
        $isUpdate = $subscriberId ? true : false;

        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:subscribers,email,' . $subscriberId,
            'mobile' => 'required|string|max:20',
            'registration_as' => 'required|string|in:Judiciary Person,Lawyer,Student,Other',
            'dob' => 'nullable|date',
            'gender' => 'nullable|string|in:Male,Female,Other',
            'address' => 'nullable|string|max:1000',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:512', // max 512 KB
            'password' => $isUpdate ? 'nullable|min:8|confirmed' : 'required|min:8|confirmed',
        ];
    }
}
