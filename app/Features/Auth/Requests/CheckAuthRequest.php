<?php

// app/Http/Requests/LoginRequest.php

declare(strict_types=1);

namespace App\Features\Auth\Requests;

use App\Core\Requests\BaseFormRequest;
use App\Features\Auth\Data\CheckAuthData;
use App\Features\Auth\Data\DeviceData;

class CheckAuthRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'device_id' => ['required', 'string'],
            'device_model' => ['nullable', 'string'],
            'device_platform' => ['required', 'string'],
            'active_business_id' => ['nullable', 'string'],
        ];
    }

    public function toDto(): CheckAuthData
    {
        return new CheckAuthData(
            deviceInfo: new DeviceData(
                deviceId: $this->input('device_id'),
                devicePlatform: $this->input('device_platform'),
                deviceModel: $this->input('device_name'),
                rememberMe: null,
                ip: $this->ip(),
            ),
            activeBusinessId: $this->input('active_business_id')
        );
    }
}
