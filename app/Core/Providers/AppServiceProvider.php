<?php

declare(strict_types=1);

namespace App\Core\Providers;

use App\Features\Auth\Events\NewDeviceDetectedEvent;
use App\Features\Auth\Mails\SendNewDeviceLoginEmail;
use App\Features\Auth\Repositories\PasswordReset\PasswordResetRepository;
use App\Features\Auth\Repositories\PasswordReset\PasswordResetRepositoryInterface;
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

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repository bindings
        $this->app->singleton(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        $this->app->singleton(
            DeviceRepositoryInterface::class,
            DeviceRepository::class
        );

        $this->app->singleton(
            PasswordResetRepositoryInterface::class,
            PasswordResetRepository::class
        );

        $this->app->singleton(
            SessionRepositoryInterface::class,
            SessionRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! app()->isProduction());

        // Configure Scramble API documentation
        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer')
                );
            });

        // Register event listeners
        Event::listen(NewDeviceDetectedEvent::class, SendNewDeviceLoginEmail::class);

    }
}
