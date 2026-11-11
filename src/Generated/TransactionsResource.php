<?php

// Gerado por scripts/generate.php a partir de ../openapi.json. Não edite à mão.

declare(strict_types=1);

namespace WyzeCash\Generated;

use WyzeCash\Requester;

final class TransactionsResource
{
    public function __construct(private readonly Requester $client)
    {
    }

    /**
     * Listar movimentações
     *
     * Movimentações com cursor (`limit` até 100) e filtros `type` e `status`; `details` traz quem pagou ou recebeu, o banco, a chave mascarada e o EndToEndId.
     *
     * **Permissão da chave:** `account:transactions`.
     *
     * `GET /v1/transactions`
     *
     * Campos:
     * - `limit`: Quantos por página (até 100).
     * - `cursor`: O `nextCursor` da página anterior.
     * - `type`: Só as movimentações deste tipo (ex.: `PIX_CHARGE`, `WITHDRAWAL`, `PIX_SENT`, `REFUND`, `MED`).
     * - `status`: Só as movimentações nesta situação.
     * - `q`: Busca.
     *
     * @param array{
     *     limit?: float|int,
     *     cursor?: string,
     *     type?: string,
     *     status?: string,
     *     q?: string
     * } $params
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return \WyzeCash\Page<array{
     *     id: string,
     *     type: string,
     *     direction: string,
     *     amount: int,
     *     fee: int,
     *     status: string,
     *     statusTitle: string,
     *     description: string,
     *     details: array{
     *         counterparty: string|null,
     *         institution: string|null,
     *         key: string|null,
     *         document: string|null,
     *         endToEndId: string|null,
     *         channel: string|null,
     *         method: string|null,
     *         card: array{
     *             brand: string,
     *             last4: string
     *         }|null,
     *         crypto: array{
     *             currency: string,
     *             network: string,
     *             amount: string,
     *             rate: string
     *         }|null,
     *         availableAt: string|null,
     *         subaccount: array{
     *             id: string,
     *             name: string,
     *             externalId: string|null
     *         }|null,
     *         keyType: string|null,
     *         reference: string|null,
     *         holderMatch: string|null,
     *         holderDocument: string|null,
     *         copyPaste: bool
     *     },
     *     createdAt: string
     * }>
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function list(array $params = [], array $options = []): \WyzeCash\Page
    {
        return $this->client->list('listar_transacoes', [], $params, $options);
    }

    /**
     * Consultar movimentação
     *
     * Uma movimentação pelo código com prefixo (BKY-, SAQ-, ENV-, TRF-, EST-, MED-, TAR-, SUB-, IND-).
     *
     * **Permissão da chave:** `account:transactions`.
     *
     * `GET /v1/transactions/{id}`
     *
     * @param string $id O código.
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     id: string,
     *     type: string,
     *     direction: string,
     *     amount: int,
     *     fee: int,
     *     status: string,
     *     statusTitle: string,
     *     description: string,
     *     details: array{
     *         counterparty: string|null,
     *         institution: string|null,
     *         key: string|null,
     *         document: string|null,
     *         endToEndId: string|null,
     *         channel: string|null,
     *         method: string|null,
     *         card: array{
     *             brand: string,
     *             last4: string
     *         }|null,
     *         crypto: array{
     *             currency: string,
     *             network: string,
     *             amount: string,
     *             rate: string
     *         }|null,
     *         availableAt: string|null,
     *         subaccount: array{
     *             id: string,
     *             name: string,
     *             externalId: string|null
     *         }|null,
     *         keyType: string|null,
     *         reference: string|null,
     *         holderMatch: string|null,
     *         holderDocument: string|null,
     *         copyPaste: bool
     *     },
     *     createdAt: string
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function retrieve(string $id, array $options = []): array
    {
        return $this->client->request('consultar_transacao', [$id], [], $options);
    }
}
