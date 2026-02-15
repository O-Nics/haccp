<?php

declare(strict_types=1);

namespace App\Modules\Auth\Requests;

use App\Core\Requests\BaseFormRequest;
use App\Core\Requests\NormalizesEmail;
use Domain\Auth\Data\ForgotPasswordData;

class ForgotPasswordRequest extends BaseFormRequest
{
    use NormalizesEmail;

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

    public function toDto(): ForgotPasswordData
    {
        return new ForgotPasswordData(
            email: $this->input('email'),
        );
    }
}
