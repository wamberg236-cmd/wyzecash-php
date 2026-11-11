<?php

declare(strict_types=1);

namespace WyzeCash\Http;

/** O que o transporte devolve: a situação HTTP, os cabeçalhos (nomes em minúsculas) e o corpo. */
final class Response
{
    /** @param array<string, string> $headers */
    public function __construct(
        public readonly int $status,
        public readonly array $headers,
        public readonly string $body,
    ) {
    }

    public function header(string $name): ?string
    {
        return $this->headers[strtolower($name)] ?? null;
    }
}
