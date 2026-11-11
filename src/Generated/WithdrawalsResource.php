<?php

// Gerado por scripts/generate.php a partir de ../openapi.json. Não edite à mão.

declare(strict_types=1);

namespace WyzeCash\Generated;

use WyzeCash\Requester;

final class WithdrawalsResource
{
    public function __construct(private readonly Requester $client)
    {
    }

    /**
     * Sacar
     *
     * Saque (SAQ-API-) por Pix para a chave enviada no pedido (`destination`) ou um Pix Copia e Cola (`copyPaste`). Pela API não existe chave favorita.
     *
     * **Permissão da chave:** `withdrawals:create`.
     *
     * Pede o cabeçalho `Idempotency-Key`.
     *
     * **Tira dinheiro da conta de verdade.**
     *
     * - A chave com `withdrawals:create` só é criada com o 2FA ligado na conta e o código do app autenticador.
     * - `feeFromAmount: true` tira a tarifa do valor: sai do saldo o `amount` e chega o `amount` menos a tarifa.
     * - O que é das subcontas não sai (`422 SUBACCOUNT_FUNDS`); o saque da subconta é outra rota.
     * - Situação: `processing` até concluir, depois `completed` (com `receiptUrl`) ou `failed`/`rejected` com o motivo. Os webhooks `withdrawal.completed` e `withdrawal.failed` avisam.
     *
     * `POST /v1/withdrawals`
     *
     * Campos:
     * - `amount`: Quanto sacar, em centavos.
     * - `destination`: Para onde vai o Pix.
     * - `copyPaste`: Pix Copia e Cola estático para onde mandar (no lugar de `destination`).
     * - `description`: A sua referência (ex.: o número do pedido). Volta no saque e no webhook.
     * - `feeFromAmount`: `true`: a tarifa sai do valor (chega o valor menos a tarifa e sai do saldo só o valor pedido). Sem ele, vale o padrão da conta.
     *
     * @param array{
     *     amount: int,
     *     destination?: array{
     *         keyType: 'cpf'|'cnpj'|'email'|'phone'|'random',
     *         key: string
     *     },
     *     copyPaste?: string,
     *     description?: string,
     *     feeFromAmount?: bool
     * } $params
     * @param array{idempotencyKey?: string, timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     id: string,
     *     status: string,
     *     method: string,
     *     amount: int,
     *     fee: int,
     *     total: int,
     *     feeFromAmount: bool,
     *     channel: string,
     *     destination: array{
     *         name: string,
     *         institution: string|null,
     *         keyType: string|null,
     *         key: string,
     *         copyPaste: bool,
     *         document: string|null,
     *         holderMatch: string|null
     *     },
     *     crypto: array{
     *         currency: string,
     *         network: string,
     *         amount: string,
     *         rate: string
     *     }|null,
     *     endToEndId: string|null,
     *     failureReason: string|null,
     *     description: string|null,
     *     subaccount: array{
     *         id: string,
     *         externalId: string|null
     *     }|null,
     *     createdAt: string,
     *     completedAt: string|null,
     *     receiptUrl: string|null
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function create(array $params, array $options = []): array
    {
        return $this->client->request('criar_saque', [], $params, $options);
    }

    /**
     * Listar saques
     *
     * Os saques da conta (concluído traz o `receiptUrl`; o da subconta traz `subaccount` e `destination.holderMatch`). `subaccountId` (o `sacc_` ou o `externalId`) traz só os saques de uma subconta.
     *
     * **Permissão da chave:** `withdrawals:list`.
     *
     * `GET /v1/withdrawals`
     *
     * Campos:
     * - `limit`: Quantos por página (até 100).
     * - `cursor`: O `nextCursor` da página anterior.
     * - `subaccountId`: Subconta que recebe a venda (o `sacc_` ou o `externalId`).
     *
     * @param array{
     *     limit?: float|int,
     *     cursor?: string,
     *     subaccountId?: string
     * } $params
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return \WyzeCash\Page<array{
     *     id: string,
     *     status: string,
     *     method: string,
     *     amount: int,
     *     fee: int,
     *     total: int,
     *     feeFromAmount: bool,
     *     channel: string,
     *     destination: array{
     *         name: string,
     *         institution: string,
     *         keyType: string,
     *         key: string,
     *         copyPaste: bool,
     *         document: string|null,
     *         holderMatch: string|null
     *     },
     *     crypto: array{
     *         currency: string,
     *         network: string,
     *         amount: string,
     *         rate: string
     *     }|null,
     *     endToEndId: string|null,
     *     failureReason: string|null,
     *     description: string|null,
     *     subaccount: array{
     *         id: string,
     *         externalId: string
     *     }|null,
     *     createdAt: string,
     *     completedAt: string|null,
     *     receiptUrl: string|null
     * }>
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function list(array $params = [], array $options = []): \WyzeCash\Page
    {
        return $this->client->list('listar_saques', [], $params, $options);
    }

    /**
     * Consultar saque
     *
     * Um saque pelo código SAQ- ou SAQ-API-.
     *
     * **Permissão da chave:** `withdrawals:get`.
     *
     * `GET /v1/withdrawals/{id}`
     *
     * @param string $id O código.
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     id: string,
     *     status: string,
     *     method: string,
     *     amount: int,
     *     fee: int,
     *     total: int,
     *     feeFromAmount: bool,
     *     channel: string,
     *     destination: array{
     *         name: string,
     *         institution: string|null,
     *         keyType: string|null,
     *         key: string,
     *         copyPaste: bool,
     *         document: string|null,
     *         holderMatch: string|null
     *     },
     *     crypto: array{
     *         currency: string,
     *         network: string,
     *         amount: string,
     *         rate: string
     *     }|null,
     *     endToEndId: string|null,
     *     failureReason: string|null,
     *     description: string|null,
     *     subaccount: array{
     *         id: string,
     *         externalId: string|null
     *     }|null,
     *     createdAt: string,
     *     completedAt: string|null,
     *     receiptUrl: string|null
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function retrieve(string $id, array $options = []): array
    {
        return $this->client->request('consultar_saque', [$id], [], $options);
    }
}
