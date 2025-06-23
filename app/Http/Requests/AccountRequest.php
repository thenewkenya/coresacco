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
            'account_type' => ['required', 'in:' . implode(',', Account::getAccountTypes())],
            'currency' => 'sometimes|string|size:3',
        ];

        // Only require member_id for staff users, not for members creating their own accounts
        if (!auth()->user()->hasRole('member')) {
            $rules['member_id'] = 'required|exists:users,id';
        }

        return $rules;
    }
} 