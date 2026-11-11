<?php

// Gerado por scripts/generate.php a partir de ../openapi.json. Não edite à mão.

declare(strict_types=1);

namespace WyzeCash\Generated;

use WyzeCash\Requester;

final class ReceiptsResource
{
    public function __construct(private readonly Requester $client)
    {
    }

    /**
     * Link do comprovante
     *
     * O link da imagem do comprovante de um saque (SAQ-), Pix enviado (ENV-) ou transferência enviada (TRF-) concluído: `{ url }`. Ainda não concluído: `409 RECEIPT_NOT_READY`.
     *
     * **Permissão da chave:** `receipts:get`.
     *
     * `GET /v1/receipts/{id}`
     *
     * @param string $id O código.
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     id: string,
     *     url: string
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function retrieve(string $id, array $options = []): array
    {
        return $this->client->request('comprovante', [$id], [], $options);
    }
}
