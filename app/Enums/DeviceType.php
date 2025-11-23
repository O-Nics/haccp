<?php

// app/Enums/DeviceType.php

declare(strict_types=1);

namespace App\Enums;

enum DeviceType: string
{
    case MOBILE = 'mobile';
    case DESKTOP = 'desktop';
    case TABLET = 'tablet';
    case UNKNOWN = 'unknown';

    public function label(): string
    {
        return match ($this) {
            self::MOBILE => 'Mobile',
            self::DESKTOP => 'Desktop',
            self::TABLET => 'Tablet',
            self::UNKNOWN => 'Unknown',
        };
    }

    public static function availableTypes(): array
    {
        return [
            self::MOBILE->value,
            self::DESKTOP->value,
            self::TABLET->value,
            self::UNKNOWN->value,
        ];
    }

    public static function fromUserAgent(?string $userAgent): self
    {
        if (! $userAgent) {
            return self::UNKNOWN;
        }

        $userAgent = strtolower($userAgent);

        return match (true) {
            str_contains($userAgent, 'mobile') ||
            str_contains($userAgent, 'android') ||
            str_contains($userAgent, 'iphone') => self::MOBILE,
            str_contains($userAgent, 'tablet') ||
            str_contains($userAgent, 'ipad') => self::TABLET,
            str_contains($userAgent, 'mozilla') ||
            str_contains($userAgent, 'chrome') ||
            str_contains($userAgent, 'safari') => self::DESKTOP,
            default => self::UNKNOWN,
        };
    }
}
