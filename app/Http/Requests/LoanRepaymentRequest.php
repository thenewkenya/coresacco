<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoanRepaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:0.01',
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Please specify the repayment amount',
            'amount.numeric' => 'The repayment amount must be a number',
            'amount.min' => 'The repayment amount must be greater than zero',
        ];
    }
} 