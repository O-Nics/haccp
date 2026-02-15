<?php

declare(strict_types=1);

namespace Infrastructure\Auth\Repositories;

use Domain\Auth\Interfaces\SessionRepositoryInterface;
use Domain\Auth\Models\Device;
use Domain\Auth\Models\Session;
use Domain\Auth\Models\User;
use Domain\Auth\ValueObjects\TokenPair;
use Throwable;

final readonly class SessionRepository implements SessionRepositoryInterface
{
    /**
     * @throws Throwable
     */
    public function createSession(User $user, Device $device, TokenPair $tokenPair): Session
    {
        $session = new Session;
        $session->device()->associate($device);
        $session->user()->associate($user);
        $session->access_token_id = $tokenPair->accessId();
        $session->refresh_token_id = $tokenPair->refreshId();
        $session->saveOrFail();

        return $session;
    }

    public function deleteSession(Session $session): void
    {
        $session->accessToken()->delete();
        $session->refreshToken()->delete();
        $session->delete();
    }
}
