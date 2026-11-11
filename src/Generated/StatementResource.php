<?php

// Gerado por scripts/generate.php a partir de ../openapi.json. Não edite à mão.

declare(strict_types=1);

namespace WyzeCash\Generated;

use WyzeCash\Requester;

final class StatementResource
{
    public function __construct(private readonly Requester $client)
    {
    }

    /**
     * Extrato
     *
     * O extrato do painel: movimentações, cobranças em aberto ou expiradas e MED (com a Taxa de MED recebido em linha própria), com os totais do período inteiro (só na primeira página). Em páginas com `limit` (até 200) e `cursor` (o `nextCursor` da página anterior). Filtros: `days` (0, 7, 30, 90), `direction`, `category`, `method` e `q` (busca).
     *
     * **Permissão da chave:** `account:statement`.
     *
     * `GET /v1/statement`
     *
     * Campos:
     * - `cursor`: O `nextCursor` da página anterior.
     * - `limit`: Quantas por página (padrão 100, até 200).
     * - `days`: Período em dias (0 = hoje).
     * - `from`: Desde.
     * - `to`: Para.
     * - `direction`: `in` (entradas) ou `out` (saídas).
     * - `category`: `pix`, `charge`, `transfer`, `withdraw`, `refund`, `med` ou `referral`.
     * - `method`: Só as movimentações desta forma: `pix` (Pix recebido, enviado, saque, estorno e MED), `card` ou `crypto` (cripto recebida e saque em cripto).
     * - `q`: Busca no código, no título, na descrição e em quem pagou ou recebeu.
     *
     * @param array{
     *     cursor?: string,
     *     limit?: float|int,
     *     days?: float|int,
     *     from?: string,
     *     to?: string,
     *     direction?: 'all'|'income'|'expense',
     *     category?: 'pix'|'charge'|'transfer'|'withdraw'|'refund'|'med'|'referral',
     *     method?: 'pix'|'card'|'crypto',
     *     q?: string
     * } $params
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return \WyzeCash\Page<array{
     *     kind: string,
     *     method: string|null,
     *     id: string,
     *     title: string,
     *     counterparty: string|null,
     *     description: string|null,
     *     category: string,
     *     amount: int,
     *     fee: int,
     *     direction: string,
     *     balanceEffect: int,
     *     status: string,
     *     statusTitle: string,
     *     tone: string,
     *     date: string,
     *     dueDate: string|null,
     *     med?: string
     * }>
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function list(array $params = [], array $options = []): \WyzeCash\Page
    {
        return $this->client->list('extrato', [], $params, $options);
    }
}
