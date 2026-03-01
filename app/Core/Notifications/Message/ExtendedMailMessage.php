<?php

declare(strict_types=1);

namespace App\Core\Notifications\Message;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class ExtendedMailMessage extends MailMessage
{
    /**
     * Adds a centered code box to the email body.
     * Suitable for displaying OTP codes, temporary passwords, or any short code
     * that needs to stand out visually.
     */
    public function box(
        string $code,
        ?int $fontSize = 28,
        ?string $fontWeight = 'bold',
        ?string $backgroundColor = '#f9fafb',
        ?string $textColor = '#111827'
    ): static
    {
        $html = <<<HTML
        <div style="text-align: center; margin: 24px 0;">
            <span style="
                display: inline-block;
                padding: 12px 24px;
                font-size: {$fontSize}px;
                font-weight: {$fontWeight};
                color: {$textColor};
                background-color: {$backgroundColor};
                border-radius: 8px;
                letter-spacing: 2px;
            ">{$code}</span>
        </div>
        HTML;

        return $this->line(new HtmlString($html));
    }
}
