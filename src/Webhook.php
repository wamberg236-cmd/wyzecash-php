<?php

declare(strict_types=1);

namespace WyzeCash;

use WyzeCash\Exception\WebhookException;

/**
 * Conferir a assinatura dos webhooks da WyzeCash. Cada aviso chega com o cabeçalho
 *   X-Bukio-Signature: t=<unix>,v1=<HMAC-SHA256 em hex de "<t>.<corpo>">
 * feito com o segredo do webhook (painel › Integrações e API › Webhooks). Confira sempre com o
 * corpo cru (`file_get_contents('php://input')`): o JSON lido e escrito de novo muda os bytes e a
 * assinatura não bate.
 */
final class Webhook
{
    public const SIGNATURE_HEADER = 'X-Bukio-Signature';

    public const EVENT_TYPES = [
        'payment.created',
        'payment.paid',
        'payment.expired',
        'payment.refunded',
        'transfer.received',
        'transfer.sent',
        'pix.sent',
        'pix.failed',
        'withdrawal.requested',
        'withdrawal.completed',
        'withdrawal.failed',
        'subaccount.credited',
        'subaccount.debited',
        'webhook.test',
    ];

    /**
     * Confere a assinatura e devolve o evento (`id`, `type`, `created_at`, `data`).
     *
     * @param int $tolerance Quantos segundos o aviso vale depois de assinado (padrão 300).
     * @param int|null $now Agora, em segundos (para testes).
     *
     * @return array{id: string, type: string, created_at: string, data: array<string, mixed>}
     *
     * @throws WebhookException quando o aviso não é da WyzeCash (assinatura errada, sem assinatura ou fora do prazo)
     */
    public static function verify(string $body, ?string $signatureHeader, string $secret, int $tolerance = 300, ?int $now = null): array
    {
        if ($secret === '') {
            throw new \InvalidArgumentException('Passe o segredo do webhook (painel › Integrações e API › Webhooks).');
        }
        if ($signatureHeader === null || $signatureHeader === '') {
            throw new WebhookException('Aviso sem o cabeçalho X-Bukio-Signature.', 'MISSING_SIGNATURE');
        }
        $timestamp = null;
        $signatures = [];
        foreach (explode(',', $signatureHeader) as $part) {
            $pair = explode('=', trim($part), 2);
            if (count($pair) !== 2) {
                continue;
            }
            if ($pair[0] === 't' && ctype_digit($pair[1])) {
                $timestamp = (int) $pair[1];
            } elseif ($pair[0] === 'v1') {
                $signatures[] = $pair[1];
            }
        }
        if ($timestamp === null || !$signatures) {
            throw new WebhookException('Cabeçalho X-Bukio-Signature em formato errado.', 'INVALID_SIGNATURE');
        }
        if (abs(($now ?? time()) - $timestamp) > $tolerance) {
            throw new WebhookException('Aviso fora do prazo (assinado há muito tempo).', 'TIMESTAMP_OUT_OF_RANGE');
        }
        $expected = hash_hmac('sha256', $timestamp . '.' . $body, $secret);
        $valid = false;
        foreach ($signatures as $signature) {
            $valid = hash_equals($expected, $signature) || $valid;
        }
        if (!$valid) {
            throw new WebhookException('A assinatura não confere: o aviso não é da WyzeCash ou o segredo está errado.', 'INVALID_SIGNATURE');
        }
        try {
            $event = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new WebhookException('O corpo do aviso não é JSON.', 'INVALID_PAYLOAD');
        }
        if (!is_array($event)) {
            throw new WebhookException('O corpo do aviso não é JSON.', 'INVALID_PAYLOAD');
        }
        /** @var array{id: string, type: string, created_at: string, data: array<string, mixed>} $event */
        return $event;
    }

    /** Gera a assinatura de um corpo (para testar o seu endpoint). */
    public static function sign(string $body, string $secret, ?int $timestamp = null): string
    {
        $t = $timestamp ?? time();
        return 't=' . $t . ',v1=' . hash_hmac('sha256', $t . '.' . $body, $secret);
    }
}
