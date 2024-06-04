<?php

namespace App\Traits\Livewire;

use Illuminate\Validation\ValidationException;

trait ValidationHandlingTrait
{
    public function handleValidationError(ValidationException $e): void
    {
        foreach ($e->errors() as $field => $messages) {
            foreach ($messages as $message) {
                $this->addError($field, $message);
            }
        }
    }
}
