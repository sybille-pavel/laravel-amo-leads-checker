<?php

namespace App\Providers;

use AmoCRM\Client\AmoCRMApiClient;
use App\Contracts\TokenStorageInterface;
use App\Services\Token\SessionTokenStorage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TokenStorageInterface::class, SessionTokenStorage::class);

        $this->app->bind(AmoCRMApiClient::class, function () {
            return (new AmoCRMApiClient(
                config('services.amocrm.client_id'),
                config('services.amocrm.client_secret'),
                config('services.amocrm.redirect')
            ));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
