<?php

declare(strict_types=1);

namespace App\Features\Device\Requests;

use App\Features\Device\Data\RevokeOthersData;
use App\Core\Requests\BaseFormRequest;

class RevokeOthersRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_device_id' => ['required', 'string', 'max:255'],
        ];
    }

    public function toDto(): RevokeOthersData
    {
        return new RevokeOthersData(
            currentDeviceId: $this->input('current_device_id'),
        );
    }
}
