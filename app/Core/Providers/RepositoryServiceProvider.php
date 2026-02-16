<?php

declare(strict_types=1);

namespace App\Core\Providers;

use Domain\Auth\Interfaces\DeviceRepositoryInterface;
use Domain\Auth\Interfaces\PasswordResetRepositoryInterface;
use Domain\Auth\Interfaces\SessionRepositoryInterface;
use Domain\Auth\Interfaces\TokenRepositoryInterface;
use Domain\Auth\Interfaces\UserRepositoryInterface;
use Domain\Business\Interfaces\BusinessRepositoryInterface;
use Domain\Business\Interfaces\MemberRepositoryInterface;
use Domain\Business\Interfaces\RoleRepositoryInterface;
use Domain\Notification\Interfaces\NotificationSentRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Auth\Repositories\DeviceRepository;
use Infrastructure\Auth\Repositories\PasswordResetRepository;
use Infrastructure\Auth\Repositories\SessionRepository;
use Infrastructure\Auth\Repositories\TokenRepository;
use Infrastructure\Auth\Repositories\UserRepository;
use Infrastructure\Business\Repositories\BusinessRepository;
use Infrastructure\Business\Repositories\MemberRepository;
use Infrastructure\Business\Repositories\RoleRepository;
use Infrastructure\Notification\Repositories\NotificationSentRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Auth domain
        $this->app->singleton(UserRepositoryInterface::class, UserRepository::class);
        $this->app->singleton(DeviceRepositoryInterface::class, DeviceRepository::class);
        $this->app->singleton(SessionRepositoryInterface::class, SessionRepository::class);
        $this->app->singleton(TokenRepositoryInterface::class, TokenRepository::class);
        $this->app->singleton(PasswordResetRepositoryInterface::class, PasswordResetRepository::class);

        // Business domain
        $this->app->singleton(BusinessRepositoryInterface::class, BusinessRepository::class);
        $this->app->singleton(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->singleton(MemberRepositoryInterface::class, MemberRepository::class);

        // Notification domain
        $this->app->singleton(NotificationSentRepositoryInterface::class, NotificationSentRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {}
}
