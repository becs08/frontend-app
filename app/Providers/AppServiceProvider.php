<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Services\ApiClient::class, function ($app) {
            return new \App\Services\ApiClient();
        });
    }

    public function boot(): void
    {
        //
    }
}
