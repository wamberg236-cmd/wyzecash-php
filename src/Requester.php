<?php

declare(strict_types=1);

namespace WyzeCash;

/** O que os recursos gerados (src/Generated) pedem ao cliente. */
interface Requester
{
    /**
     * @param list<string> $pathArgs
     * @param array<string, mixed> $params
     * @param array{idempotencyKey?: string, timeout?: float|int, maxRetries?: int} $options
     */
    public function request(string $operationId, array $pathArgs, array $params, array $options = []): mixed;

    /**
     * @param list<string> $pathArgs
     * @param array<string, mixed> $params
     * @param array{idempotencyKey?: string, timeout?: float|int, maxRetries?: int} $options
     *
     * @return Page<mixed>
     */
    public function list(string $operationId, array $pathArgs, array $params, array $options = []): Page;
}
