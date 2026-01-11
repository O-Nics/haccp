<?php

// app/Http/Requests/RefreshTokenRequest.php

declare(strict_types=1);

namespace App\Features\Auth\Requests;

use App\Core\Requests\BaseFormRequest;
use App\Features\Auth\Data\RefreshTokenData;

class RefreshTokenRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'refresh_token' => ['required', 'string'],
            'device_id' => ['required', 'string'],
        ];
    }

    public function toDto(): RefreshTokenData
    {
        return new RefreshTokenData(
            refreshToken: $this->input('refresh_token'),
            deviceId: $this->input('device_id'),
        );
    }
}
