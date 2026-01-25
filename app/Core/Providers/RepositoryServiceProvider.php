<?php

declare(strict_types=1);

namespace App\Core\Providers;

use App\Features\Auth\Repositories\Auth\AuthRepository;
use App\Features\Auth\Repositories\Auth\AuthRepositoryInterface;
use App\Features\Auth\Repositories\PasswordReset\PasswordResetRepository;
use App\Features\Auth\Repositories\PasswordReset\PasswordResetRepositoryInterface;
use App\Features\Business\Repositories\BusinessRepository;
use App\Features\Business\Repositories\BusinessRepositoryInterface;
use App\Features\Notifications\Repositories\NotificationSent\NotificationSentRepository;
use App\Features\Notifications\Repositories\NotificationSent\NotificationSentRepositoryInterface;
use App\Features\Role\Repositories\Role\RoleRepository;
use App\Features\Role\Repositories\Role\RoleRepositoryInterface;
use App\Features\User\Repositories\User\UserRepository;
use App\Features\User\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(UserRepositoryInterface::class, UserRepository::class);
        $this->app->singleton(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->singleton(PasswordResetRepositoryInterface::class, PasswordResetRepository::class);
        $this->app->singleton(BusinessRepositoryInterface::class, BusinessRepository::class);
        $this->app->singleton(NotificationSentRepositoryInterface::class, NotificationSentRepository::class);
        $this->app->singleton(RoleRepositoryInterface::class, RoleRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {}
}
