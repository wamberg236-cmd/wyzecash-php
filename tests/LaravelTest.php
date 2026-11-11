<?php

declare(strict_types=1);

use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use WyzeCash\Laravel\VerifyWebhookSignature;
use WyzeCash\Laravel\WyzeCashServiceProvider;
use WyzeCash\Webhook;
use WyzeCash\WyzeCash;

if (!class_exists(\Illuminate\Support\ServiceProvider::class)) {
    require __DIR__ . '/stubs/laravel.php';
}

final class TestConfig implements Repository
{
    /** @param array<string, mixed> $values */
    public function __construct(public array $values = [])
    {
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->values;
        foreach (explode('.', $key) as $part) {
            if (!is_array($value) || !array_key_exists($part, $value)) {
                return $default;
            }
            $value = $value[$part];
        }
        return $value;
    }

    public function set(string $key, mixed $value): void
    {
        $this->values[$key] = $value;
    }
}

final class TestApp implements \Illuminate\Contracts\Foundation\Application
{
    /** @var array<string, Closure> */
    public array $bindings = [];

    /** @var array<string, string> */
    public array $aliases = [];

    public function __construct(public TestConfig $config, public Router $router)
    {
    }

    public function singleton(string $abstract, Closure $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    public function alias(string $abstract, string $alias): void
    {
        $this->aliases[$alias] = $abstract;
    }

    public function make(string $abstract): mixed
    {
        return match ($abstract) {
            'config' => $this->config,
            'router' => $this->router,
            default => ($this->bindings[$this->aliases[$abstract] ?? $abstract])($this),
        };
    }

    public function runningInConsole(): bool
    {
        return true;
    }

    public function configPath(string $path = ''): string
    {
        return '/app/config/' . $path;
    }
}

test('Laravel: o provider põe o cliente no container e o middleware wyzecash.webhook', function (): void {
    $app = new TestApp(new TestConfig(['wyzecash' => ['api_key' => 'bk_live_laravel']]), new Router());
    $provider = new WyzeCashServiceProvider($app);
    $provider->register();
    $provider->boot();
    $client = $app->make(WyzeCash::class);
    assertTrue($client instanceof WyzeCash);
    assertTrue($app->make('wyzecash') instanceof WyzeCash);
    assertSame(300, $app->config->get('wyzecash')['webhook_tolerance']);
    assertSame(VerifyWebhookSignature::class, $app->router->aliases['wyzecash.webhook']);
    assertSame('/app/config/wyzecash.php', array_values(\Illuminate\Support\ServiceProvider::$published['wyzecash-config'])[0]);
});

test('Laravel: o middleware deixa passar o aviso certo e guarda o evento', function (): void {
    $body = '{"id":"evt_9","type":"payment.paid","created_at":"2026-11-11T12:00:00.000Z","data":{"id":"BKY-1"}}';
    $config = new TestConfig(['wyzecash' => ['webhook_secret' => 'whsec_laravel']]);
    $request = new Request($body, ['X-Bukio-Signature' => Webhook::sign($body, 'whsec_laravel')]);
    $seen = null;
    $response = (new VerifyWebhookSignature($config))->handle($request, function (Request $r) use (&$seen) {
        $seen = $r->attributes->get('wyzecash_event');
        return new \Illuminate\Http\Response('ok', 200);
    });
    assertSame(200, $response->status);
    assertSame('evt_9', $seen['id']);
});

test('Laravel: o middleware recusa o aviso forjado com 400', function (): void {
    $body = '{"id":"evt_9","type":"payment.paid"}';
    $config = new TestConfig(['wyzecash' => ['webhook_secret' => 'whsec_laravel']]);
    $request = new Request($body, ['X-Bukio-Signature' => Webhook::sign($body, 'outro-segredo')]);
    $called = false;
    $response = (new VerifyWebhookSignature($config))->handle($request, function () use (&$called) {
        $called = true;
        return new \Illuminate\Http\Response('ok');
    });
    assertSame(400, $response->status);
    assertTrue(!$called, 'a rota não pode rodar');
});
