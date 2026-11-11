<?php

// Gerado por scripts/generate.php a partir de ../openapi.json. Não edite à mão.

declare(strict_types=1);

namespace WyzeCash\Generated;

use WyzeCash\Requester;

final class BalanceResource
{
    public function __construct(private readonly Requester $client)
    {
    }

    /**
     * Saldo
     *
     * Disponível, bloqueado e a liberar, quanto é das subcontas (`subaccounts`) e o livre (`free`), e os limites de saque do dia (`withdrawal`).
     *
     * **Permissão da chave:** `account:balance`.
     *
     * `GET /v1/balance`
     *
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     available: int,
     *     subaccounts: int,
     *     free: int,
     *     pending: int,
     *     blocked: int,
     *     medOwed: int,
     *     withdrawal: array{
     *         minPerTransaction: int
     *     },
     *     updatedAt: string
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function retrieve(array $options = []): array
    {
        return $this->client->request('saldo', [], [], $options);
    }
}
