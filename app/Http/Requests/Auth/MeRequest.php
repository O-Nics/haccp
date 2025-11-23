<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\DataTransferObjects\Auth\MeData;
use App\Http\Requests\Base\BaseFormRequest;

class MeRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function toDto(): MeData
    {
        return new MeData;
    }
}
