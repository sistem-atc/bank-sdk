<?php

declare(strict_types=1);

namespace SistemAtc\Banks;

use Illuminate\Support\ServiceProvider;

class BanksServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/banks.php', 'banks'
        );
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/banks.php' => config_path('banks.php'),
            ], 'banks-config');
        }
    }
}
