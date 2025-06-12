<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'member_id' => 'required|exists:users,id',
            'loan_type_id' => 'required|exists:loan_types,id',
            'amount' => 'required|numeric|min:0.01',
            'term_period' => 'required|integer|min:1',
            'collateral_details' => 'required|array',
            'collateral_details.type' => 'required|string',
            'collateral_details.value' => 'required|numeric',
            'collateral_details.description' => 'required|string',
        ];
    }
} 