<?php

declare(strict_types=1);

namespace App\Modules\Auth\Requests;

use App\Core\Requests\BaseFormRequest;
use Domain\Auth\Data\ForgotPasswordData;

class ForgotPasswordRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim($this->input('email', ''))),
        ]);
    }

    public function toDto(): ForgotPasswordData
    {
        return new ForgotPasswordData(
            email: $this->input('email'),
        );
    }
}
