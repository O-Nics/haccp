<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\NewDeviceDetected;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendNewDeviceLoginEmail implements ShouldQueue
{
    public function handle(NewDeviceDetected $event): void
    {
        // TODO: Send email to user about new device login
        // Mail::to($event->user)->send(new NewDeviceLoginMail($event->device));
    }
}
