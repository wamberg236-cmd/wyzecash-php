<?php

declare(strict_types=1);

use WyzeCash\Exception\ConnectionException;
use WyzeCash\Exception\WyzeCashException;
use WyzeCash\Http\Response;
use WyzeCash\Page;
use WyzeCash\Tests\FakeTransport;
use WyzeCash\WyzeCash;

function client(FakeTransport $fake, array $options = []): WyzeCash
{
    return new WyzeCash('bk_live_teste', $options + ['transport' => $fake, 'sleep' => static function (float $s): void {
    }]);
}

test('manda a chave, o corpo e a Idempotency-Key', function (): void {
    $fake = new FakeTransport([FakeTransport::json(200, ['id' => 'BKY-8K2M4QZ7XN3P', 'status' => 'pending'])]);
    $wyzecash = new WyzeCash('bk_live_teste', ['baseUrl' => 'https://api.exemplo.com/', 'transport' => $fake]);
    $charge = $wyzecash->payments->create(['amount' => 15000, 'description' => 'Pedido #1052'], ['idempotencyKey' => 'pedido-1052']);
    assertSame('BKY-8K2M4QZ7XN3P', $charge['id']);
    $call = $fake->calls[0];
    assertSame('https://api.exemplo.com/v1/payments', $call['url']);
    assertSame('POST', $call['method']);
    assertSame('Bearer bk_live_teste', $call['headers']['authorization']);
    assertSame('pedido-1052', $call['headers']['idempotency-key']);
    assertSame('application/json', $call['headers']['content-type']);
    assertTrue(str_starts_with($call['headers']['user-agent'], 'wyzecash-php/'));
    assertSame('{"amount":15000,"description":"Pedido #1052"}', $call['body']);
});

test('cria a Idempotency-Key sozinho e repete a mesma no 429', function (): void {
    $fake = new FakeTransport([
        FakeTransport::json(429, ['error' => ['code' => 'RATE_LIMITED', 'message' => 'Muitas tentativas.']], ['retry-after' => '0']),
        FakeTransport::json(200, ['id' => 'BKY-1']),
    ]);
    client($fake)->payments->create(['amount' => 1000]);
    assertSame(2, count($fake->calls));
    $key = $fake->calls[0]['headers']['idempotency-key'];
    assertTrue(preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $key) === 1, 'uuid v4');
    assertSame($key, $fake->calls[1]['headers']['idempotency-key']);
});

test('consulta: caminho e consulta na URL, sem corpo', function (): void {
    $fake = new FakeTransport([FakeTransport::json(200, ['id' => 'sacc_1']), FakeTransport::json(200, ['items' => [], 'nextCursor' => null])]);
    $wyzecash = client($fake);
    $wyzecash->subaccounts->retrieve('cliente 1/a');
    $wyzecash->subaccounts->listEntries('sacc_1', ['kind' => 'sale', 'limit' => 10, 'from' => '2026-11-01', 'to' => null]);
    assertSame('/v1/subaccounts/cliente%201%2Fa', $fake->path(0));
    assertSame(null, $fake->calls[0]['body']);
    assertSame('/v1/subaccounts/sacc_1/entries', $fake->path(1));
    assertSame(['kind' => 'sale', 'limit' => '10', 'from' => '2026-11-01'], $fake->query(1));
});

test('null vai como null no corpo e corpo vazio vai como objeto', function (): void {
    $fake = new FakeTransport([FakeTransport::json(200, []), FakeTransport::json(200, [])]);
    $wyzecash = client($fake);
    $wyzecash->subaccounts->updateSettings(['limits' => ['perWithdrawal' => null]]);
    assertSame('{"limits":{"perWithdrawal":null}}', $fake->calls[0]['body']);
    assertSame('PUT', $fake->calls[0]['method']);
    $wyzecash->subaccounts->updateSettings();
    assertSame('{}', $fake->calls[1]['body']);
});

test('erro da API vira WyzeCashException com o código e o requestId', function (): void {
    $fake = new FakeTransport([FakeTransport::json(422, ['error' => ['code' => 'INSUFFICIENT_FUNDS', 'message' => 'Saldo insuficiente.'], 'requestId' => 'req-1'])]);
    $error = assertThrows(WyzeCashException::class, fn () => client($fake)->withdrawals->create(['amount' => 10000, 'destination' => ['keyType' => 'email', 'key' => 'financeiro@exemplo.com']]));
    assertSame(422, $error->status);
    assertSame(422, $error->getCode());
    assertSame('INSUFFICIENT_FUNDS', $error->errorCode);
    assertSame('INSUFFICIENT_FUNDS', $error->getErrorCode());
    assertSame('Saldo insuficiente.', $error->getMessage());
    assertSame('req-1', $error->requestId);
});

test('erro sem JSON vira HTTP_status', function (): void {
    $fake = new FakeTransport([new Response(502, ['x-request-id' => 'req-2'], '<html>')]);
    $error = assertThrows(WyzeCashException::class, fn () => client($fake, ['maxRetries' => 0])->balance->retrieve());
    assertSame('HTTP_502', $error->errorCode);
    assertSame('req-2', $error->requestId);
});

test('dinheiro saindo nunca repete sozinho em erro de rede', function (): void {
    $fake = new FakeTransport([new ConnectionException('caiu'), FakeTransport::json(200, [])]);
    $error = assertThrows(ConnectionException::class, fn () => client($fake)->pix->send(['amount' => 1000, 'destination' => ['keyType' => 'email', 'key' => 'a@exemplo.com']]));
    assertSame('CONNECTION_ERROR', $error->errorCode);
    assertSame(0, $error->status);
    assertSame(1, count($fake->calls));
});

test('dinheiro saindo também não repete no 500', function (): void {
    $fake = new FakeTransport([FakeTransport::json(500, ['error' => ['code' => 'INTERNAL', 'message' => 'Erro.']]), FakeTransport::json(200, [])]);
    $error = assertThrows(WyzeCashException::class, fn () => client($fake)->subaccounts->fund('sacc_1', ['amount' => 1000]));
    assertSame('INTERNAL', $error->errorCode);
    assertSame(1, count($fake->calls));
});

test('criar cobrança repete no 5xx com a mesma chave', function (): void {
    $fake = new FakeTransport([FakeTransport::json(503, ['error' => ['code' => 'UNAVAILABLE', 'message' => 'Fora.']]), FakeTransport::json(200, ['id' => 'BKY-1'])]);
    client($fake)->payments->create(['amount' => 1000]);
    assertSame(2, count($fake->calls));
    assertSame($fake->calls[0]['headers']['idempotency-key'], $fake->calls[1]['headers']['idempotency-key']);
});

test('consulta repete em erro de rede e passa o tempo do pedido', function (): void {
    $fake = new FakeTransport([new ConnectionException('recusou'), FakeTransport::json(200, ['available' => 100])]);
    $balance = client($fake, ['maxRetries' => 1])->balance->retrieve(['timeout' => 5]);
    assertSame(100, $balance['available']);
    assertSame(2, count($fake->calls));
    assertSame(5.0, $fake->calls[0]['timeout']);
});

test('espera o Retry-After no 429', function (): void {
    $waits = [];
    $fake = new FakeTransport([FakeTransport::json(429, [], ['retry-after' => '3']), FakeTransport::json(200, ['available' => 1])]);
    $wyzecash = new WyzeCash('bk_live_teste', ['transport' => $fake, 'sleep' => static function (float $s) use (&$waits): void {
        $waits[] = $s;
    }]);
    $wyzecash->balance->retrieve();
    assertSame([3.0], $waits);
});

test('lista: a primeira página e o foreach passa por todas', function (): void {
    $fake = new FakeTransport([
        FakeTransport::json(200, ['items' => [['id' => 'BKY-1'], ['id' => 'BKY-2']], 'nextCursor' => 'c2']),
        FakeTransport::json(200, ['items' => [['id' => 'BKY-3']], 'nextCursor' => null]),
    ]);
    $page = client($fake)->payments->list(['status' => 'paid']);
    assertTrue($page instanceof Page);
    assertSame(2, count($page->items));
    assertTrue($page->hasMore());
    assertSame(1, count($fake->calls));
    $ids = [];
    foreach ($page as $charge) {
        $ids[] = $charge['id'];
    }
    assertSame(['BKY-1', 'BKY-2', 'BKY-3'], $ids);
    assertSame(['status' => 'paid', 'cursor' => 'c2'], $fake->query(1));
});

test('toArray para no limite', function (): void {
    $fake = new FakeTransport([FakeTransport::json(200, ['items' => [['id' => '1'], ['id' => '2']], 'nextCursor' => 'c2'])]);
    assertSame([['id' => '1'], ['id' => '2']], client($fake)->withdrawals->list()->toArray(2));
    assertSame(1, count($fake->calls));
});

test('arquivo da prova do MED volta como texto cru', function (): void {
    $fake = new FakeTransport([new Response(200, ['content-type' => 'application/pdf'], "\x01\x02\x03")]);
    assertSame("\x01\x02\x03", client($fake)->meds->downloadAttachment('MED-1', 'anexo-1'));
    assertSame('*/*', $fake->calls[0]['headers']['accept']);
    assertSame('/v1/meds/MED-1/attachments/anexo-1', $fake->path(0));
});

test('código vazio na rota avisa', function (): void {
    assertThrows(InvalidArgumentException::class, fn () => client(new FakeTransport([]))->payments->retrieve(''));
});

test('sem chave avisa e lê a variável', function (): void {
    $saved = getenv('WYZECASH_API_KEY');
    putenv('WYZECASH_API_KEY');
    try {
        $error = assertThrows(InvalidArgumentException::class, fn () => new WyzeCash());
        assertTrue(str_contains($error->getMessage(), 'chave de API'));
        putenv('WYZECASH_API_KEY=bk_live_env');
        $fake = new FakeTransport([FakeTransport::json(200, [])]);
        (new WyzeCash(null, ['transport' => $fake]))->balance->retrieve();
        assertSame('Bearer bk_live_env', $fake->calls[0]['headers']['authorization']);
    } finally {
        putenv($saved === false ? 'WYZECASH_API_KEY' : 'WYZECASH_API_KEY=' . $saved);
    }
});

test('var_dump não mostra a chave', function (): void {
    ob_start();
    var_dump(client(new FakeTransport([])));
    assertTrue(!str_contains((string) ob_get_clean(), 'bk_live_teste'));
});
