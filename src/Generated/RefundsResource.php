<?php

// Gerado por scripts/generate.php a partir de ../openapi.json. Não edite à mão.

declare(strict_types=1);

namespace WyzeCash\Generated;

use WyzeCash\Requester;

final class RefundsResource
{
    public function __construct(private readonly Requester $client)
    {
    }

    /**
     * Estornar uma cobrança
     *
     * Estorno (EST-) total ou parcial de uma cobrança paga no Pix (`transactionId` = o BKY-). Só no Pix.
     *
     * **Permissão da chave:** `refunds:create`.
     *
     * Pede o cabeçalho `Idempotency-Key`.
     *
     * **Tira dinheiro da conta de verdade.**
     *
     * - A chave com `refunds:create` só é criada com o 2FA ligado na conta e o código do app autenticador.
     * - Recusado com `MED_IN_PROGRESS` se a venda tem MED aberto ou em análise.
     * - Concluído, o webhook `payment.refunded` avisa.
     *
     * `POST /v1/refunds`
     *
     * Campos:
     * - `transactionId`: A venda paga a estornar (`BKY-`).
     * - `amount`: Quanto estornar, em centavos. Sem ele, estorna o que falta da venda.
     * - `reason`: Motivo do estorno (vai para o extrato).
     *
     * @param array{
     *     transactionId: string,
     *     amount?: int,
     *     reason?: string
     * } $params
     * @param array{idempotencyKey?: string, timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     id: string,
     *     status: string,
     *     amount: int,
     *     reason: string|null,
     *     originalTransaction: string,
     *     endToEndId: string|null,
     *     failureReason: string|null,
     *     createdAt: string,
     *     completedAt: string|null
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function create(array $params, array $options = []): array
    {
        return $this->client->request('criar_estorno', [], $params, $options);
    }

    /**
     * Listar estornos
     *
     * Os estornos da conta.
     *
     * **Permissão da chave:** `refunds:list`.
     *
     * `GET /v1/refunds`
     *
     * Campos:
     * - `limit`: Quantos por página (até 100).
     * - `cursor`: O `nextCursor` da página anterior.
     *
     * @param array{
     *     limit?: float|int,
     *     cursor?: string
     * } $params
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return \WyzeCash\Page<array{
     *     id: string,
     *     status: string,
     *     amount: int,
     *     reason: string|null,
     *     originalTransaction: string,
     *     endToEndId: string,
     *     failureReason: string|null,
     *     createdAt: string,
     *     completedAt: string
     * }>
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function list(array $params = [], array $options = []): \WyzeCash\Page
    {
        return $this->client->list('listar_estornos', [], $params, $options);
    }

    /**
     * Consultar estorno
     *
     * Um estorno pelo código EST-.
     *
     * **Permissão da chave:** `refunds:get`.
     *
     * `GET /v1/refunds/{id}`
     *
     * @param string $id O código.
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     id: string,
     *     status: string,
     *     amount: int,
     *     reason: string,
     *     originalTransaction: string,
     *     endToEndId: string,
     *     failureReason: string|null,
     *     createdAt: string,
     *     completedAt: string
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function retrieve(string $id, array $options = []): array
    {
        return $this->client->request('consultar_estorno', [$id], [], $options);
    }
}
