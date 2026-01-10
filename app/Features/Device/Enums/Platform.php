<?php

// app/Enums/Platform.php

declare(strict_types=1);

namespace App\Features\Device\Enums;

enum Platform: string
{
    case IOS = 'ios';
    case ANDROID = 'android';
    case WINDOWS = 'windows';
    case MACOS = 'macos';
    case LINUX = 'linux';
    case WEB = 'web';
    case UNKNOWN = 'unknown';

    public function label(): string
    {
        return match ($this) {
            self::IOS => 'iOS',
            self::ANDROID => 'Android',
            self::WINDOWS => 'Windows',
            self::MACOS => 'macOS',
            self::LINUX => 'Linux',
            self::WEB => 'Web',
            self::UNKNOWN => 'Unknown',
        };
    }

    public static function availablePlatforms(): array
    {
        return [
            self::IOS->value,
            self::ANDROID->value,
            self::WINDOWS->value,
            self::MACOS->value,
            self::LINUX->value,
            self::WEB->value,
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
            str_contains($userAgent, 'iphone') ||
            str_contains($userAgent, 'ipad') ||
            str_contains($userAgent, 'ipod') => self::IOS,
            str_contains($userAgent, 'android') => self::ANDROID,
            str_contains($userAgent, 'windows') => self::WINDOWS,
            str_contains($userAgent, 'mac os') ||
            str_contains($userAgent, 'macintosh') => self::MACOS,
            str_contains($userAgent, 'linux') => self::LINUX,
            default => self::WEB,
        };
    }
}
