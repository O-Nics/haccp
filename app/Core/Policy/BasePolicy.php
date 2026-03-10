<?php

declare(strict_types=1);

namespace App\Core\Policy;

use Domain\Auth\Models\User;
use Domain\Business\Enums\PermissionEnum;
use Domain\Business\Models\Business;
use Illuminate\Auth\Access\Response;

class BasePolicy
{
    protected function hasPermission(User $user, Business $business, PermissionEnum $permission): Response
    {
        if ($user->memberOf($business)?->isOwner() || $user->memberOf($business)?->hasPermission($permission)) {
            return $this->allow();
        }

        return $this->deny("Vous n'avez pas la permission de faire cette action.");
    }

    protected function check(bool $condition, string $message): Response
    {
        if ($condition) {
            return $this->allow();
        }

        return $this->deny($message);
    }

    protected function deny(string $message): Response
    {
        if (app()->isProduction()) {
            $message = 'Forbidden';
        }

        return Response::deny($message);
    }

    protected function allow(): Response
    {
        return Response::allow();
    }
}
