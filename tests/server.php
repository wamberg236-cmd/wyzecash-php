<?php

// Servidor local para o TransportTest (php -S): responde como a API, sem rede.

declare(strict_types=1);

header('Content-Type: application/json');
$path = (string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($path === '/v1/balance') {
    http_response_code(401);
    echo json_encode(['error' => ['code' => 'UNAUTHORIZED', 'message' => 'Chave de API inválida.'], 'requestId' => 'req-9']);
    return;
}
if ($path === '/v1/payments' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = json_decode((string) file_get_contents('php://input'), true);
    header('X-Request-Id: req-10');
    echo json_encode([
        'id' => 'BKY-2',
        'amount' => $body['amount'] ?? null,
        'key' => $_SERVER['HTTP_IDEMPOTENCY_KEY'] ?? null,
        'auth' => $_SERVER['HTTP_AUTHORIZATION'] ?? null,
    ]);
    return;
}
echo json_encode(['items' => [['id' => 'BKY-1']], 'nextCursor' => null, 'query' => $_SERVER['QUERY_STRING'] ?? '']);
