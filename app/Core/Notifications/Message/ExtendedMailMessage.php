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
    public function box(string $code): static
    {
        $html = <<<HTML
        <div style="text-align: center; margin: 24px 0;">
            <span style="
                display: inline-block;
                padding: 14px 32px;
                font-size: 28px;
                font-weight: 700;
                letter-spacing: 6px;
                font-family: 'Courier New', Courier, monospace;
                color: #111827;
                background-color: #f9fafb;
                border: 2px solid #e5e7eb;
                border-radius: 10px;
            ">{$code}</span>
        </div>
        HTML;

        return $this->line(new HtmlString($html));
    }
}
