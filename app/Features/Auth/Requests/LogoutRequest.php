<?php

// app/Http/Requests/LoginRequest.php

declare(strict_types=1);

namespace App\Features\Auth\Requests;

use App\Core\Requests\BaseFormRequest;
use App\Features\Auth\Data\LogoutData;

class LogoutRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'device_id' => ['required', 'string'],
        ];
    }

    /**
     * Convert request to DTO
     */
    public function toDto(): LogoutData
    {
        return new LogoutData(
            deviceId: $this->input('device_id'),
        );
    }
}
