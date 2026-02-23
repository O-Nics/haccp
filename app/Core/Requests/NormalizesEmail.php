<?php

declare(strict_types=1);

namespace App\Core\Requests;

trait NormalizesEmail
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim($this->input('email', ''))),
        ]);
    }
}
