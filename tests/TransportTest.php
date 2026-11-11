<?php

declare(strict_types=1);

use WyzeCash\Exception\ConnectionException;
use WyzeCash\Exception\WyzeCashException;
use WyzeCash\WyzeCash;

/** Sobe o php -S com o tests/server.php numa porta livre e devolve [porta, processo]. */
function startServer(): array
{
    $probe = stream_socket_server('tcp://127.0.0.1:0');
    $port = (int) substr((string) stream_socket_get_name($probe, false), strrpos((string) stream_socket_get_name($probe, false), ':') + 1);
    fclose($probe);
    $process = proc_open([PHP_BINARY, '-S', '127.0.0.1:' . $port, __DIR__ . '/server.php'], [1 => ['file', '/dev/null', 'w'], 2 => ['file', '/dev/null', 'w']], $pipes);
    for ($i = 0; $i < 100; $i++) {
        $socket = @fsockopen('127.0.0.1', $port, $errno, $errstr, 0.1);
        if ($socket) {
            fclose($socket);
            return [$port, $process];
        }
        usleep(50_000);
    }
    throw new RuntimeException('o php -S não subiu');
}

test('cURL de verdade: sucesso, consulta e erro da API', function (): void {
    [$port, $process] = startServer();
    try {
        $wyzecash = new WyzeCash('bk_live_teste', ['baseUrl' => 'http://127.0.0.1:' . $port, 'maxRetries' => 0]);
        $created = $wyzecash->payments->create(['amount' => 1500], ['idempotencyKey' => 'pedido-9']);
        assertSame(['id' => 'BKY-2', 'amount' => 1500, 'key' => 'pedido-9', 'auth' => 'Bearer bk_live_teste'], $created);

        $ids = [];
        $page = $wyzecash->payments->list(['status' => 'paid', 'limit' => 5]);
        foreach ($page as $charge) {
            $ids[] = $charge['id'];
        }
        assertSame(['BKY-1'], $ids);
        assertSame('status=paid&limit=5', $page['query']);

        $error = assertThrows(WyzeCashException::class, fn () => $wyzecash->balance->retrieve());
        assertSame([401, 'UNAUTHORIZED', 'req-9'], [$error->status, $error->errorCode, $error->requestId]);
    } finally {
        proc_terminate($process);
        proc_close($process);
    }
});

test('cURL de verdade: porta fechada vira erro de conexão', function (): void {
    $probe = stream_socket_server('tcp://127.0.0.1:0');
    $name = (string) stream_socket_get_name($probe, false);
    fclose($probe);
    $wyzecash = new WyzeCash('bk_live_teste', ['baseUrl' => 'http://' . $name, 'maxRetries' => 0]);
    $error = assertThrows(ConnectionException::class, fn () => $wyzecash->balance->retrieve());
    assertSame('CONNECTION_ERROR', $error->errorCode);
});
