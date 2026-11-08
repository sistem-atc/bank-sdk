<?php

declare(strict_types=1);

namespace SistemAtc\Banks;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use SistemAtc\Banks\Http\Controllers\BankWebhookController;

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
        $this->registerWebhookRoutes();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/banks.php' => config_path('banks.php'),
            ], 'banks-config');
        }
    }

    /**
     * Uma rota por banco listado em `banks.webhooks.routes`:
     *
     *   POST {prefix}/{banco}/{integração}/{tipo}[/{sufixo}]
     *
     * O sufixo opcional existe porque o Inter acrescenta `/pix` à URL
     * cadastrada no webhook de Pix recebido (padrão do Bacen).
     */
    protected function registerWebhookRoutes(): void
    {
        $banks = array_values(array_filter((array) config('banks.webhooks.routes', [])));

        if ($banks === []) {
            return;
        }

        Route::prefix(trim((string) config('banks.webhooks.prefix', 'api/webhooks/banks'), '/'))
            ->middleware((array) config('banks.webhooks.middleware', ['api']))
            ->group(function () use ($banks) {
                foreach ($banks as $bank) {
                    Route::post("/{$bank}/{integration}/{tipo}/{sufixo?}", BankWebhookController::class)
                        ->defaults('bank', $bank)
                        ->where('integration', '[A-Za-z0-9_-]+')
                        ->where('tipo', '[a-z0-9-]+')
                        ->where('sufixo', '.*')
                        ->name("banks.webhooks.{$bank}");
                }
            });
    }
}
