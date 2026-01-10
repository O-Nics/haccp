<?php

// app/Http/Requests/RefreshTokenRequest.php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Features\Auth\Data\RefreshTokenData;
use App\Http\Requests\Base\BaseFormRequest;

class RefreshTokenRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'refresh_token' => ['required', 'string', 'size:64'],
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
