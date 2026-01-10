<?php

declare(strict_types=1);

namespace App\Features\Auth\Mails;

use App\Events\NewDeviceDetectedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendNewDeviceLoginEmail implements ShouldQueue
{
    public function handle(NewDeviceDetectedEvent $event): void
    {
        // TODO: Send email to user about new device login
        // Mail::to($event->user)->send(new NewDeviceLoginMail($event->device));
    }
}
