<?php

declare(strict_types=1);

namespace App\Modules\Auth\Requests;

use App\Core\Requests\BaseFormRequest;
use Domain\Auth\Data\DeviceData;
use Domain\Auth\Data\RegisterData;
use Domain\Auth\Data\StoreUserData;
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
            'email' => app()->environment(['local', 'testing'])
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
            'device_id' => ['required', 'string'],
            'device_model' => ['nullable', 'string'],
            'device_platform' => ['required', 'string'],

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
            storeUserData: new StoreUserData(
                firstName: $this->input('first_name'),
                lastName: $this->input('last_name'),
                email: $this->input('email'),
                password: $this->input('password'),
            ),
            deviceInfo: new DeviceData(
                deviceId: $this->input('device_id'),
                devicePlatform: $this->input('device_platform'),
                deviceModel: $this->input('device_name'),
                rememberMe: $this->boolean('remember_me'),
                ip: $this->ip()
            ),
        );
    }
}
