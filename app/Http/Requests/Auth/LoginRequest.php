<?php

// app/Http/Requests/LoginRequest.php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\DataTransferObjects\Auth\LoginData;
use App\Http\Requests\Base\BaseFormRequest;

class LoginRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => app()->environment('testing')
                ? ['required', 'email', 'max:255']
                : ['required', 'email:rfc,dns', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'remember_me' => ['boolean'],
        ];
    }

    /**
     * Prepare the data for validation
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim($this->input('email', ''))),
        ]);
    }

    /**
     * Convert request to DTO
     */
    public function toDto(): LoginData
    {
        return new LoginData(
            email: $this->input('email'),
            password: $this->input('password'),
            rememberMe: $this->boolean('remember_me'),
        );
    }
}
