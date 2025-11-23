<?php

// app/Events/UserLoggedIn.php

declare(strict_types=1);

namespace App\Events;

use App\Models\Device;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserLoggedIn
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly Device $device
    ) {}
}
