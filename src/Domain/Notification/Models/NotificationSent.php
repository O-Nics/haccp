<?php

declare(strict_types=1);

namespace Domain\Notification\Models;

use App\Core\Models\BaseModel;
use Domain\Auth\Models\User;
use Domain\Notification\Enums\NotificationChannel;
use Domain\Notification\Enums\NotificationSentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $user_id
 * @property string $recipient
 * @property string $name
 * @property NotificationChannel $type
 * @property NotificationSentStatus $status
 * @property string|null $subject
 * @property string|null $content_html
 * @property string|null $content_text
 * @property \Illuminate\Support\Carbon|null $resent_at
 * @property string|null $error_message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $user
 *
 * @mixin \Eloquent
 */
class NotificationSent extends BaseModel
{
    use HasFactory;

    protected static function newFactory(): \Domain\Notification\Database\Factories\NotificationSentFactory
    {
        return \Domain\Notification\Database\Factories\NotificationSentFactory::new();
    }

    protected function casts(): array
    {
        return [
            'type' => NotificationChannel::class,
            'status' => NotificationSentStatus::class,
            'resent_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
