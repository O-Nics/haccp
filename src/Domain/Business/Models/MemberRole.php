<?php

declare(strict_types=1);

namespace Domain\Business\Models;

use App\Core\Models\BasePivot;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberRole extends BasePivot
{
    protected $table = 'member_role';

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
