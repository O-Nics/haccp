<?php

declare(strict_types=1);

namespace Infrastructure\Auth\Repositories;

use App\Core\Repositories\BaseRepository;
use Domain\Auth\Interfaces\SessionRepositoryInterface;
use Domain\Auth\Models\Device;
use Domain\Auth\Models\Session;
use Domain\Auth\Models\User;
use Domain\Auth\ValueObjects\TokenPair;
use Throwable;

final readonly class SessionRepository extends BaseRepository implements SessionRepositoryInterface
{
    public function __construct(
        private Session $model
    ) {}

    /**
     * @throws Throwable
     */
    public function createSession(User $user, Device $device, TokenPair $tokenPair): Session
    {
        $session = $this->model->newInstance();
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

    public function revokeAllForUser(User $user): void
    {
        $sessions = $user->sessions()->with(['accessToken', 'refreshToken'])->get();

        foreach ($sessions as $session) {
            $this->deleteSession($session);
        }
    }
}
