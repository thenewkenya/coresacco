<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Account;

class AccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'account_type' => ['required', 'in:' . implode(',', Account::ACCOUNT_TYPES)],
            'initial_deposit' => 'nullable|numeric|min:0',
            'currency' => 'sometimes|string|size:3',
        ];

        // Only require member_id for staff users, not for members creating their own accounts
        if (!auth()->user()->hasRole('member')) {
            $rules['member_id'] = 'required|exists:users,id';
            
            // Prevent duplicate account types for the same member
            if ($this->member_id && $this->account_type) {
                $rules['account_type'][] = 'unique:accounts,account_type,NULL,id,member_id,' . $this->member_id;
            }
        }

        return $rules;
    }
} 