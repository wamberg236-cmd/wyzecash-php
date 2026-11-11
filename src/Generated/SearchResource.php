<?php

// Gerado por scripts/generate.php a partir de ../openapi.json. Não edite à mão.

declare(strict_types=1);

namespace WyzeCash\Generated;

use WyzeCash\Requester;

final class SearchResource
{
    public function __construct(private readonly Requester $client)
    {
    }

    /**
     * Buscar na conta
     *
     * Busca movimentações, cobranças, MED e clientes pelo código, descrição, nome ou valor (de 2 a 80 caracteres).
     *
     * **Permissão da chave:** `account:search`.
     *
     * `GET /v1/search`
     *
     * Campos:
     * - `q`: O que buscar: código, nome, descrição ou valor.
     *
     * @param array{
     *     q: string
     * } $params
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     movements: list<array{
     *         kind: string,
     *         id: string,
     *         title: string,
     *         counterparty: string|null,
     *         description: string,
     *         category: string,
     *         amount: int,
     *         balanceEffect: int,
     *         direction: string,
     *         status: string,
     *         statusTitle: string,
     *         tone: string,
     *         date: string
     *     }>,
     *     customers: list<array{
     *         id: string,
     *         name: string
     *     }>
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function query(array $params, array $options = []): array
    {
        return $this->client->request('buscar', [], $params, $options);
    }
}
