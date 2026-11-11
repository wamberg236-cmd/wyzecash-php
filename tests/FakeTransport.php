<?php

declare(strict_types=1);

namespace WyzeCash\Tests;

use WyzeCash\Http\Response;
use WyzeCash\Http\Transport;

/** Transporte de mentira: devolve as respostas na ordem e guarda cada pedido. */
final class FakeTransport implements Transport
{
    /** @var list<array{method: string, url: string, headers: array<string, string>, body: ?string, timeout: float}> */
    public array $calls = [];

    /** @param list<Response|\Throwable> $responses */
    public function __construct(private array $responses)
    {
    }

    public function send(string $method, string $url, array $headers, ?string $body, float $timeout): Response
    {
        $this->calls[] = ['method' => $method, 'url' => $url, 'headers' => array_change_key_case($headers), 'body' => $body, 'timeout' => $timeout];
        $next = array_shift($this->responses) ?? throw new \LogicException('sem resposta preparada');
        if ($next instanceof \Throwable) {
            throw $next;
        }
        return $next;
    }

    /** @param array<string, string> $headers */
    public static function json(int $status, mixed $body, array $headers = []): Response
    {
        return new Response($status, ['content-type' => 'application/json'] + $headers, (string) json_encode($body));
    }

    /** @return array<string, string> */
    public function query(int $call): array
    {
        parse_str((string) parse_url($this->calls[$call]['url'], PHP_URL_QUERY), $query);
        /** @var array<string, string> $query */
        return $query;
    }

    public function path(int $call): string
    {
        return (string) parse_url($this->calls[$call]['url'], PHP_URL_PATH);
    }
}
