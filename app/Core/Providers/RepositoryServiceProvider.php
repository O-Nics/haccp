<?php

declare(strict_types=1);

namespace App\Core\Providers;

use App\Core\Events\NewDeviceDetectedEvent;
use App\Features\Auth\Mails\SendNewDeviceLoginEmail;
use App\Features\Auth\Repositories\PasswordReset\PasswordResetRepository;
use App\Features\Auth\Repositories\PasswordReset\PasswordResetRepositoryInterface;
use App\Features\Business\Repositories\BusinessRepository;
use App\Features\Business\Repositories\BusinessRepositoryInterface;
use App\Features\Session\Repositories\Device\DeviceRepository;
use App\Features\Session\Repositories\Device\DeviceRepositoryInterface;
use App\Features\Session\Repositories\Session\SessionRepository;
use App\Features\Session\Repositories\Session\SessionRepositoryInterface;
use App\Features\User\Repositories\User\UserRepository;
use App\Features\User\Repositories\User\UserRepositoryInterface;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(UserRepositoryInterface::class, UserRepository::class);
        $this->app->singleton(DeviceRepositoryInterface::class, DeviceRepository::class);
        $this->app->singleton(PasswordResetRepositoryInterface::class, PasswordResetRepository::class);
        $this->app->singleton(SessionRepositoryInterface::class, SessionRepository::class);
        $this->app->singleton(BusinessRepositoryInterface::class, BusinessRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void{}
}
