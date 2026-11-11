<?php

// Gerado por scripts/generate.php a partir de ../openapi.json. Não edite à mão.

declare(strict_types=1);

namespace WyzeCash\Generated;

use WyzeCash\Requester;

final class FeesResource
{
    public function __construct(private readonly Requester $client)
    {
    }

    /**
     * Tarifas da conta
     *
     * A tarifa de cada operação (`PIX_CHARGE`, `CARD_CHARGE`, `CRYPTO_CHARGE`, `WITHDRAWAL`, `CRYPTO_WITHDRAWAL`, `INTERNAL_TRANSFER`, `MED`): `fixedCents`, `percentBps` e `minCents` (o valor mínimo da operação). `WITHDRAWAL` vale para todo Pix que sai: saque, Pix enviado e Copia e Cola. Vale a negociada para a conta, ou a da plataforma.
     *
     * **Permissão da chave:** `account:fees`.
     *
     * `GET /v1/fees`
     *
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     fees: list<array{
     *         operation: string,
     *         fixedCents: int,
     *         percentBps: int,
     *         minCents: int,
     *         custom: bool
     *     }>
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function list(array $options = []): array
    {
        return $this->client->request('tarifas', [], [], $options);
    }

    /**
     * Prévia da tarifa de um valor
     *
     * Quanto a WyzeCash cobra numa operação de um valor (calculado no servidor, nunca no seu sistema).
     *
     * **Permissão da chave:** `account:fees`.
     *
     * `GET /v1/fees/preview`
     *
     * Campos:
     * - `operation`: A operação: `PIX_CHARGE`, `CARD_CHARGE`, `CRYPTO_CHARGE`, `WITHDRAWAL`, `CRYPTO_WITHDRAWAL` ou `INTERNAL_TRANSFER`.
     * - `amount`: O valor, em centavos.
     *
     * @param array{
     *     operation: string,
     *     amount: float|int
     * } $params
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     operation: string,
     *     amount: int,
     *     fee: int
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function preview(array $params, array $options = []): array
    {
        return $this->client->request('previa_tarifa', [], $params, $options);
    }
}
