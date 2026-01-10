<?php

// app/Http/Requests/LoginRequest.php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Features\Auth\Data\LogoutData;
use App\Http\Requests\Base\BaseFormRequest;

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
