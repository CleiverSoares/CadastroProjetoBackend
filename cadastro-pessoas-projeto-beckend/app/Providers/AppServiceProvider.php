<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\NotificacaoService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(NotificacaoService::class, function ($app) {
            return new NotificacaoService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
