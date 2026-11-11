<?php

declare(strict_types=1);

namespace WyzeCash\Laravel;

use Closure;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use WyzeCash\Exception\WebhookException;
use WyzeCash\Webhook;

/**
 * Confere a assinatura do webhook (X-Bukio-Signature) com o corpo cru. Aviso que não é da WyzeCash
 * recebe 400 e não chega à sua rota; o que passa fica em `$request->attributes->get('wyzecash_event')`.
 *
 *   Route::post('/webhooks/wyzecash', WebhookController::class)->middleware('wyzecash.webhook');
 *
 * Tire a rota do CSRF (em bootstrap/app.php, `validateCsrfTokens(except: ['webhooks/wyzecash'])`).
 */
final class VerifyWebhookSignature
{
    public function __construct(private readonly Repository $config)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $secret = $this->config->get('wyzecash.webhook_secret');
        if (!is_string($secret) || $secret === '') {
            throw new \RuntimeException('Defina WYZECASH_WEBHOOK_SECRET (config/wyzecash.php, webhook_secret).');
        }
        $tolerance = $this->config->get('wyzecash.webhook_tolerance', 300);
        try {
            $event = Webhook::verify($request->getContent(), $request->headers->get(Webhook::SIGNATURE_HEADER), $secret, is_int($tolerance) ? $tolerance : 300);
        } catch (WebhookException $error) {
            return new \Illuminate\Http\JsonResponse(['error' => $error->getMessage()], 400);
        }
        $request->attributes->set('wyzecash_event', $event);
        $response = $next($request);
        return $response instanceof Response ? $response : new \Illuminate\Http\Response($response);
    }
}
