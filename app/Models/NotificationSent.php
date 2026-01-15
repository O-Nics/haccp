<?php

namespace App\Models;

use App\Features\Notifications\Enums\NotificationChannel;
use App\Features\Notifications\Enums\NotificationSentStatus;
use App\Models\Core\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationSent extends BaseModel
{
    use HasFactory;

    // Mutators

    protected function casts(): array
    {
        return [
            'type' => NotificationChannel::class,
            'status' => NotificationSentStatus::class,
            'resent_at' => 'datetime',
        ];
    }

    // Relations

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}

