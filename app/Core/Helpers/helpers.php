<?php

declare(strict_types=1);

use Illuminate\Support\Str;

if (! function_exists('to_slug')) {
    function to_slug(string $name): string
    {
        return Str::slug($name);
    }
}
