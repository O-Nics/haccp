<?php

declare(strict_types=1);

namespace App\Core\Providers;

use App\Features\Business\Repositories\BusinessRepository;
use App\Features\Business\Repositories\BusinessRepositoryInterface;
use App\Features\BusinessRole\Repositories\BusinessRoleRepository;
use App\Features\BusinessRole\Repositories\BusinessRoleRepositoryInterface;
use App\Features\Notifications\Repositories\NotificationSent\NotificationSentRepository;
use App\Features\Notifications\Repositories\NotificationSent\NotificationSentRepositoryInterface;
use Domain\Auth\Interfaces\DeviceRepositoryInterface;
use Domain\Auth\Interfaces\PasswordResetRepositoryInterface;
use Domain\Auth\Interfaces\SessionRepositoryInterface;
use Domain\Auth\Interfaces\TokenRepositoryInterface;
use Domain\Auth\Interfaces\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Auth\Repositories\DeviceRepository;
use Infrastructure\Auth\Repositories\PasswordResetRepository;
use Infrastructure\Auth\Repositories\SessionRepository;
use Infrastructure\Auth\Repositories\TokenRepository;
use Infrastructure\Auth\Repositories\UserRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Auth domain repositories
        $this->app->singleton(UserRepositoryInterface::class, UserRepository::class);
        $this->app->singleton(DeviceRepositoryInterface::class, DeviceRepository::class);
        $this->app->singleton(SessionRepositoryInterface::class, SessionRepository::class);
        $this->app->singleton(TokenRepositoryInterface::class, TokenRepository::class);
        $this->app->singleton(PasswordResetRepositoryInterface::class, PasswordResetRepository::class);

        // Business domain repositories
        $this->app->singleton(BusinessRepositoryInterface::class, BusinessRepository::class);
        $this->app->singleton(NotificationSentRepositoryInterface::class, NotificationSentRepository::class);
        $this->app->singleton(BusinessRoleRepositoryInterface::class, BusinessRoleRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {}
}
