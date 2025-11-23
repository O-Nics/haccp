<?php

namespace App\Http\Requests\Base;

use BackedEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Optional;

abstract class BaseFormRequest extends FormRequest
{
    public function value(string $key): mixed
    {
        return $this->exists($key)
            ? $this->input($key)
            : new Optional;
    }

    public function valueFile(string $key): mixed
    {
        return $this->hasFile($key) ? $this->file($key) : new Optional;
    }

    public function valueEnum(string $key, string $enumClass): Optional|BackedEnum
    {
        /* @var class-string<BackedEnum> $enumClass */
        if (! $this->exists($key)) {
            return new Optional;
        }

        $value = $this->input($key);

        return $enumClass::tryFrom($value) ?? new Optional;
    }

    public function valueCarbon(string $key): Optional|Carbon
    {
        if (! $this->exists($key)) {
            return new Optional;
        }

        return Carbon::parse($this->input($key));
    }
}
