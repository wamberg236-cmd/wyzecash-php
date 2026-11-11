<?php

declare(strict_types=1);

namespace WyzeCash\Laravel;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use WyzeCash\WyzeCash;

/**
 * Integração com o Laravel (carregada sozinha pelo package discovery): o cliente `WyzeCash` no
 * container (com a chave do config/wyzecash.php), a facade `WyzeCash` e o middleware
 * `wyzecash.webhook`, que confere a assinatura dos webhooks.
 */
final class WyzeCashServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/wyzecash.php', 'wyzecash');
        $this->app->singleton(WyzeCash::class, static function (Application $app): WyzeCash {
            /** @var array{api_key?: string|null, timeout?: int|float, max_retries?: int} $config */
            $config = $app->make('config')->get('wyzecash', []);
            return new WyzeCash($config['api_key'] ?? null, [
                'timeout' => $config['timeout'] ?? 60,
                'maxRetries' => $config['max_retries'] ?? 2,
            ]);
        });
        $this->app->alias(WyzeCash::class, 'wyzecash');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../../config/wyzecash.php' => $this->app->configPath('wyzecash.php')], 'wyzecash-config');
        }
        /** @var Router $router */
        $router = $this->app->make('router');
        $router->aliasMiddleware('wyzecash.webhook', VerifyWebhookSignature::class);
    }
}
