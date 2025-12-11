<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionRequest extends FormRequest
{
    // You can declare a constant for repeated rules
    private const NUMERIC_REQUIRED = 'required|numeric';

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
            'subscriber_id'     => 'required|numeric|exists:subscribers,id',
            'package_id'        => 'required|numeric|exists:packages,id',
            'subscription_date' => 'required|date',
            'expire_date'       => 'required|date|after_or_equal:subscription_date',
            'fee'               => 'required|numeric|min:0',
            'payment_method'    => 'nullable|string|max:50',
            'transaction_id'    => 'nullable|string|max:100',
            'status'            => 'required|in:0,1,2,3',
            'remarks'           => 'nullable|string',
        ];
    }
}
