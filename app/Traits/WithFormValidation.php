<?php

namespace App\Traits;

use Illuminate\Support\MessageBag;

trait WithFormValidation
{
    public MessageBag $customErrors;

    public function initializeWithFormValidation(): void
    {
        $this->customErrors = new MessageBag;
    }

    protected function getRealTimeValidationRules(): array
    {
        return [];
    }

    public function validateField(string $field): void
    {
        $rules = $this->getRealTimeValidationRules();
        
        if (isset($rules[$field])) {
            try {
                $this->validateOnly($field, $rules);
                $this->customErrors->forget($field);
            } catch (\Exception $e) {
                // Do nothing, let Laravel handle the error
            }
        }
    }
} 