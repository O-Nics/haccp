<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\DataTransferObjects\Auth\RegisterData;
use App\Http\Requests\Base\BaseFormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => app()->environment('testing')
                ? ['required', 'email', 'max:255', 'unique:users,email']
                : ['required', 'email:rfc,dns', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols(),
            ],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
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
    public function toDto(): RegisterData
    {
        return new RegisterData(
            first_name: $this->input('first_name'),
            last_name: $this->input('last_name'),
            email: $this->input('email'),
            password: $this->input('password'),
            remember_me: $this->boolean('remember_me'),
        );
    }
}
