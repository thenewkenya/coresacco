<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone_number' => 'required|string|max:20',
            'id_number' => 'required|string|max:20|unique:users,id_number',
            'address' => 'required|string|max:500',
            'branch_id' => 'required|exists:branches,id',
        ];

        // If this is an update request, modify unique rules
        if ($this->method() === 'PUT' || $this->method() === 'PATCH') {
            $rules['email'] = 'required|email|max:255|unique:users,email,' . $this->member->id;
            $rules['id_number'] = 'required|string|max:20|unique:users,id_number,' . $this->member->id;
        }

        // If this is a create request, require password
        if ($this->method() === 'POST') {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        return $rules;
    }
} 