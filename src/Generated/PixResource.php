<?php

// Gerado por scripts/generate.php a partir de ../openapi.json. Não edite à mão.

declare(strict_types=1);

namespace WyzeCash\Generated;

use WyzeCash\Requester;

final class PixResource
{
    public function __construct(private readonly Requester $client)
    {
    }

    /**
     * Enviar Pix
     *
     * Envia um Pix (ENV-) para uma chave (`destination`) ou paga um Pix Copia e Cola (`copyPaste`).
     *
     * **Permissão da chave:** `pix:create`.
     *
     * Pede o cabeçalho `Idempotency-Key`.
     *
     * **Tira dinheiro da conta de verdade.**
     *
     * - A chave com `pix:create` só é criada com o 2FA ligado na conta e o código do app autenticador.
     * - A resposta vem `processing` até concluir; o webhook `pix.sent` (com `receiptUrl`) ou `pix.failed` avisa o resultado.
     *
     * `POST /v1/pix/payments`
     *
     * Campos:
     * - `amount`: Valor do Pix, em centavos. Com Copia e Cola que já tem valor, pode ficar sem ou precisa ser o mesmo.
     * - `destination`: Para onde vai o Pix.
     * - `copyPaste`: Pix Copia e Cola estático para pagar (no lugar de `destination`).
     * - `description`: A sua referência (ex.: o número do pedido). Volta no Pix enviado e no webhook.
     *
     * @param array{
     *     amount?: int,
     *     destination?: array{
     *         keyType: 'cpf'|'cnpj'|'email'|'phone'|'random',
     *         key: string
     *     },
     *     copyPaste?: string,
     *     description?: string
     * } $params
     * @param array{idempotencyKey?: string, timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     id: string,
     *     status: string,
     *     amount: int,
     *     fee: int,
     *     total: int,
     *     channel: string,
     *     description: string|null,
     *     destination: array{
     *         name: string,
     *         institution: string|null,
     *         keyType: string|null,
     *         key: string,
     *         copyPaste: bool
     *     },
     *     endToEndId: string|null,
     *     failureReason: string|null,
     *     createdAt: string,
     *     completedAt: string|null,
     *     receiptUrl: string|null
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function send(array $params = [], array $options = []): array
    {
        return $this->client->request('enviar_pix', [], $params, $options);
    }

    /**
     * Listar Pix enviados
     *
     * Os Pix enviados (concluído traz o `receiptUrl`).
     *
     * **Permissão da chave:** `pix:list`.
     *
     * `GET /v1/pix/payments`
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
     *     fee: int,
     *     total: int,
     *     channel: string,
     *     description: string|null,
     *     destination: array{
     *         name: string,
     *         institution: string,
     *         keyType: string,
     *         key: string,
     *         copyPaste: bool
     *     },
     *     endToEndId: string,
     *     failureReason: string|null,
     *     createdAt: string,
     *     completedAt: string,
     *     receiptUrl: string
     * }>
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function list(array $params = [], array $options = []): \WyzeCash\Page
    {
        return $this->client->list('listar_pix_enviados', [], $params, $options);
    }

    /**
     * Consultar Pix enviado
     *
     * Um Pix enviado pelo código ENV-.
     *
     * **Permissão da chave:** `pix:get`.
     *
     * `GET /v1/pix/payments/{id}`
     *
     * @param string $id O código.
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     id: string,
     *     status: string,
     *     amount: int,
     *     fee: int,
     *     total: int,
     *     channel: string,
     *     description: string|null,
     *     destination: array{
     *         name: string,
     *         institution: string,
     *         keyType: string,
     *         key: string,
     *         copyPaste: bool
     *     },
     *     endToEndId: string|null,
     *     failureReason: string|null,
     *     createdAt: string,
     *     completedAt: string|null,
     *     receiptUrl: string|null
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function retrieve(string $id, array $options = []): array
    {
        return $this->client->request('consultar_pix_enviado', [$id], [], $options);
    }
}
