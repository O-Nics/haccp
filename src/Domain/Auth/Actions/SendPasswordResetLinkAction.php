<?php

declare(strict_types=1);

namespace Domain\Auth\Actions;

use App\Core\Actions\BaseAction;
use Domain\Auth\Data\ForgotPasswordData;
use Domain\Auth\Events\PasswordResetRequestedEvent;
use Domain\Auth\Interfaces\PasswordResetRepositoryInterface;
use Domain\Auth\Interfaces\UserRepositoryInterface;
use Domain\Auth\Models\PasswordResetToken;

final readonly class SendPasswordResetLinkAction extends BaseAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordResetRepositoryInterface $passwordResetRepository,
    ) {}

    public function handle(ForgotPasswordData $data): void
    {
        $user = $this->userRepository->findByEmail($data->email);

        if (! $user) {
            return;
        }

        $this->passwordResetRepository->deleteAllByEmail($data->email);

        $token = PasswordResetToken::generateToken();
        $expirationMinutes = config('auth.password_reset.expiration', 60);

        $this->passwordResetRepository->store(
            $data->email,
            $token,
            $expirationMinutes
        );

        event(new PasswordResetRequestedEvent($user, $token));
    }
}
