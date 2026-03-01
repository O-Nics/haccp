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
     * Creates and persists a new auth session linking the user, device, and token pair.
     *
     * @param  User  $user  The authenticated user.
     * @param  Device  $device  The device the session is created on.
     * @param  TokenPair  $tokenPair  The issued access and refresh token pair.
     *
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

    /**
     * Revokes and hard-deletes the session along with its access and refresh tokens.
     *
     * @param  Session  $session  The session to delete.
     */
    public function deleteSession(Session $session): void
    {
        $session->accessToken()->delete();
        $session->refreshToken()->delete();
        $session->delete();
    }

    /**
     * Revokes all active sessions for the given user.
     *
     * @param  User  $user  The user whose sessions to revoke.
     */
    public function revokeAllForUser(User $user): void
    {
        $sessions = $user->sessions()->with(['accessToken', 'refreshToken'])->get();

        foreach ($sessions as $session) {
            $this->deleteSession($session);
        }
    }

    /**
     * Replaces the session's access token with a new one and deletes the old token.
     *
     * @param  Session  $session  The session to update.
     * @param  int  $newAccessTokenId  The ID of the newly issued access token.
     */
    public function rotateAccessToken(Session $session, int $newAccessTokenId): void
    {
        $oldAccessToken = $session->accessToken;
        $session->access_token_id = $newAccessTokenId;
        $session->save();
        $oldAccessToken?->delete();
    }
}
