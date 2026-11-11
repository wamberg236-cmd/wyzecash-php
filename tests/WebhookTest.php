<?php

declare(strict_types=1);

use WyzeCash\Exception\WebhookException;
use WyzeCash\Webhook;

const WEBHOOK_SECRET = 'whsec_teste';
const WEBHOOK_BODY = '{"id":"evt_1","type":"payment.paid","created_at":"2026-10-15T12:00:00.000Z","data":{"id":"BKY-1","amount":15000}}';
const WEBHOOK_NOW = 1_760_000_000;
// O mesmo vetor dos SDKs de Node e Python (assinatura do backend, user-webhooks.delivery.ts).
const NODE_SIGNATURE = 't=1760000000,v1=f39a04e4c49d71ad1ea598fd91ee342500d245274be15352801eb0b39dc6ea31';

function backendSignature(int $t, string $raw, string $key = WEBHOOK_SECRET): string
{
    return 't=' . $t . ',v1=' . hash_hmac('sha256', $t . '.' . $raw, $key);
}

test('webhook: aceita a assinatura do backend e devolve o evento', function (): void {
    assertSame(NODE_SIGNATURE, backendSignature(WEBHOOK_NOW, WEBHOOK_BODY));
    $event = Webhook::verify(WEBHOOK_BODY, NODE_SIGNATURE, WEBHOOK_SECRET, 300, WEBHOOK_NOW);
    assertSame('payment.paid', $event['type']);
    assertSame(15000, $event['data']['amount']);
    assertSame('X-Bukio-Signature', Webhook::SIGNATURE_HEADER);
});

test('webhook: sign faz a mesma assinatura do backend', function (): void {
    assertSame(NODE_SIGNATURE, Webhook::sign(WEBHOOK_BODY, WEBHOOK_SECRET, WEBHOOK_NOW));
});

test('webhook: recusa segredo errado, corpo mexido, sem cabeçalho e fora do prazo', function (): void {
    $cases = [
        [WEBHOOK_BODY, backendSignature(WEBHOOK_NOW, WEBHOOK_BODY, 'outro'), 'INVALID_SIGNATURE'],
        [str_replace('15000', '99999', WEBHOOK_BODY), backendSignature(WEBHOOK_NOW, WEBHOOK_BODY), 'INVALID_SIGNATURE'],
        [WEBHOOK_BODY, null, 'MISSING_SIGNATURE'],
        [WEBHOOK_BODY, 'lixo', 'INVALID_SIGNATURE'],
        [WEBHOOK_BODY, backendSignature(WEBHOOK_NOW - 301, WEBHOOK_BODY), 'TIMESTAMP_OUT_OF_RANGE'],
        ['nao e json', backendSignature(WEBHOOK_NOW, 'nao e json'), 'INVALID_PAYLOAD'],
    ];
    foreach ($cases as [$raw, $signature, $code]) {
        $error = assertThrows(WebhookException::class, fn () => Webhook::verify($raw, $signature, WEBHOOK_SECRET, 300, WEBHOOK_NOW));
        assertSame($code, $error->errorCode, $code);
        assertSame(400, $error->status);
    }
});

test('webhook: aceita mais de uma assinatura v1', function (): void {
    $header = NODE_SIGNATURE . ',v1=' . str_repeat('0', 64);
    assertSame('evt_1', Webhook::verify(WEBHOOK_BODY, $header, WEBHOOK_SECRET, 300, WEBHOOK_NOW)['id']);
});

test('webhook: sem segredo avisa', function (): void {
    assertThrows(InvalidArgumentException::class, fn () => Webhook::verify(WEBHOOK_BODY, NODE_SIGNATURE, '', 300, WEBHOOK_NOW));
});
