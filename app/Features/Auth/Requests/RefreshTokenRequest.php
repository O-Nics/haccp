<?php

// app/Http/Requests/RefreshTokenRequest.php

declare(strict_types=1);

namespace App\Features\Auth\Requests;

use App\Features\Auth\Data\RefreshTokenData;
use App\Core\Requests\BaseFormRequest;

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
