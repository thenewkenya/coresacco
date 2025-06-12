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
        return [
            'member_id' => 'required|exists:users,id',
            'account_type' => 'required|in:savings,shares,deposits',
            'currency' => 'sometimes|string|size:3',
        ];
    }
} 