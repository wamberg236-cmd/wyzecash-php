<?php

declare(strict_types=1);

namespace WyzeCash\Http;

use WyzeCash\Exception\ConnectionException;

/** Quem faz o pedido HTTP. Troque para testar ou para passar por um proxy próprio. */
interface Transport
{
    /**
     * @param array<string, string> $headers
     *
     * @throws ConnectionException quando o pedido não chegou à API ou a resposta não voltou
     */
    public function send(string $method, string $url, array $headers, ?string $body, float $timeout): Response;
}
