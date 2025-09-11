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

        // Types that allow multiple accounts per member
        $multiAllowed = ['deposits', 'junior', 'goal_based', 'business'];

        if (auth()->user()->hasRole('member')) {
            // For members, if member_id is provided, it must be their own ID
            if ($this->has('member_id')) {
                $rules['member_id'] = 'required|in:' . auth()->user()->id;
            }
            
            // Prevent duplicate account types for the member
            if ($this->account_type && !in_array($this->account_type, $multiAllowed, true)) {
                $rules['account_type'][] = 'unique:accounts,account_type,NULL,id,member_id,' . auth()->user()->id;
            }
        } else {
            // Staff users must specify a valid member_id
            $rules['member_id'] = 'required|exists:users,id';
            
            // Prevent duplicate account types for the same member
            if ($this->member_id && $this->account_type && !in_array($this->account_type, $multiAllowed, true)) {
                $rules['account_type'][] = 'unique:accounts,account_type,NULL,id,member_id,' . $this->member_id;
            }
        }

        return $rules;
    }
} 