<?php

declare(strict_types=1);

namespace App\Core\Providers;

use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Domain\Auth\Events\NewDeviceDetectedEvent;
use Domain\Auth\Events\PasswordResetCompletedEvent;
use Domain\Auth\Events\PasswordResetRequestedEvent;
use Domain\Auth\Events\UserRegisteredEvent;
use Domain\Auth\Listeners\NewDeviceDetectedListener;
use Domain\Auth\Listeners\SendPasswordResetConfirmedNotificationListener;
use Domain\Auth\Listeners\SendPasswordResetNotificationListener;
use Domain\Auth\Listeners\SendUserRegisteredNotificationListener;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;
use Domain\Business\Policies\BusinessPolicy;
use Domain\Business\Policies\MemberPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

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

        // Register policies
        Gate::policy(Member::class, MemberPolicy::class);
        Gate::policy(Business::class, BusinessPolicy::class);

        // Register event listeners
        Event::listen(NewDeviceDetectedEvent::class, NewDeviceDetectedListener::class);
        Event::listen(UserRegisteredEvent::class, SendUserRegisteredNotificationListener::class);
        Event::listen(PasswordResetRequestedEvent::class, SendPasswordResetNotificationListener::class);
        Event::listen(PasswordResetCompletedEvent::class, SendPasswordResetConfirmedNotificationListener::class);
    }
}
