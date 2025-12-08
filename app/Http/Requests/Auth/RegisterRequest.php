<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\DataTransferObjects\Auth\RegisterData;
use App\DataTransferObjects\DeviceInfoData;
use App\Enums\DeviceType;
use App\Enums\Platform;
use App\Http\Requests\Base\BaseFormRequest;
use Illuminate\Validation\Rule;
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

            'device_name' => ['required', 'string', 'max:255'],
            'device_id' => ['required', 'string'],
            'device_type' => ['nullable', Rule::enum(DeviceType::class)],
            'platform' => ['nullable', 'string', 'max:50'],
            'browser' => ['nullable', 'string', 'max:100'],
            'user_agent' => ['nullable', 'string', 'max:512'],
            'fingerprint' => ['nullable', 'string', 'max:512'],

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
            firstName: $this->input('first_name'),
            lastName: $this->input('last_name'),
            email: $this->input('email'),
            password: $this->input('password'),
            rememberMe: $this->boolean('remember_me'),
            deviceInfo: new DeviceInfoData(
                deviceId: $this->input('device_id'),
                deviceName: $this->input('device_name'),
                deviceType: $this->input('device_type')
                    ? DeviceType::from($this->input('device_type'))
                    : DeviceType::fromUserAgent($this->input('user_agent') ?? $this->userAgent()),
                platform: $this->input('platform')
                    ? Platform::from(strtolower($this->input('platform')))
                    : Platform::fromUserAgent($this->input('user_agent') ?? $this->userAgent()),
                browser: $this->input('browser'),
                userAgent: $this->input('user_agent') ?? $this->userAgent(),
            )
        );
    }
}
