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
 * @method static Builder<static>|NotificationSent betweenDates($column, ?string $startAt = null, ?string $endAt = null, $boolean = 'and', $not = false)
 * @method static \Domain\Notification\Database\Factories\NotificationSentFactory factory($count = null, $state = [])
 * @method static Builder<static>|NotificationSent filtered(?string $filterQuery = null, array $searchableColumns = [])
 * @method static Builder<static>|NotificationSent forPeriod(?string $startAt = null, ?string $endAt = null, ?string $columnStart = 'start_at', ?string $columnEnd = 'end_at')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSent newQuery()
 * @method static Builder<static>|NotificationSent order(?string $sort, ?string $order)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSent whereContentHtml($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSent whereContentText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSent whereErrorMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSent whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSent whereRecipient($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSent whereResentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSent whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSent whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSent whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSent whereUserId($value)
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
