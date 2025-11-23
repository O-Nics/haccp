<?php

// app/Http/Requests/LoginRequest.php

declare(strict_types=1);

namespace App\Http\Requests\Device;

use App\DataTransferObjects\DeviceInfoData;
use App\Enums\DeviceType;
use App\Enums\Platform;
use App\Http\Requests\Base\BaseFormRequest;
use Illuminate\Validation\Rule;

class DeviceRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
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
     * Convert request to DTO
     */
    public function toDto(): DeviceInfoData
    {
        return new DeviceInfoData(
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
        );
    }
}
