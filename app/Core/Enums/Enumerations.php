<?php

declare(strict_types=1);

namespace App\Core\Enums;

use BackedEnum;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use ReflectionEnum;
use UnitEnum;

use function array_column;
use function array_rand;
use function is_subclass_of;

/**
 * @template T of BackedEnum|UnitEnum
 */
trait Enumerations
{
    /**
     * @return Collection<array-key, self>
     */
    public static function collect(): Collection
    {
        return collect(static::cases());
    }

    /**
     * @return string[]
     */
    public static function names(): array
    {
        return array_column(static::cases(), 'name');
    }

    /**
     * @return (static is \IntBackedEnum ? int[] : string[])
     */
    public static function values(): array
    {
        return array_column(static::cases(), match (true) {
            static::isUnit() => 'name',
            default => 'value',
        });
    }

    public static function isUnit(): bool
    {
        return static::isBacked() === false;
    }

    public static function isBacked(): bool
    {
        return is_subclass_of(static::class, BackedEnum::class);
    }

    public static function isStringBacked(): bool
    {
        return new ReflectionEnum(static::class)->getBackingType()->getName() === 'string';
    }

    public static function isIntBacked(): bool
    {
        return new ReflectionEnum(static::class)->getBackingType()->getName() === 'integer';
    }

    public function is(self $type): bool
    {
        return $this === $type;
    }

    public static function rule(): In
    {
        return Rule::in(static::cases());
    }

    public static function random(): self
    {
        return static::cases()[array_rand(static::cases())];
    }
}
