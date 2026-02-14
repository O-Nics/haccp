<?php

// app/Http/Requests/LoginRequest.php

declare(strict_types=1);

namespace App\Modules\Auth\Requests;

use App\Core\Requests\BaseFormRequest;
use Domain\Auth\Data\DeviceData;
use Domain\Auth\Data\LoginData;

class LoginRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => app()->environment(['local', 'testing'])
                ? ['required', 'email', 'max:255']
                : ['required', 'email:rfc,dns', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'remember_me' => ['boolean'],
            'device_id' => ['required', 'string'],
            'device_model' => ['nullable', 'string'],
            'device_platform' => ['required', 'string'],
            'active_business_id' => ['nullable', 'string'],
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
            deviceData: new DeviceData(
                deviceId: $this->input('device_id'),
                devicePlatform: $this->input('device_platform'),
                deviceModel: $this->input('device_name'),
                rememberMe: $this->boolean('remember_me'),
                ip: $this->ip()
            ),
            activeBusiness: $this->input('active_business_id')
        );
    }
}
