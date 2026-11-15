<?php

// Gerado por scripts/generate.php a partir de ../openapi.json. Não edite à mão.

declare(strict_types=1);

namespace WyzeCash\Generated;

use WyzeCash\Requester;

final class MedsResource
{
    public function __construct(private readonly Requester $client)
    {
    }

    /**
     * Listar MED
     *
     * Os pedidos de devolução (MED) das vendas no Pix: valor, quanto está bloqueado, prazo e situação (`open` Aberto, `analysis` Em análise, `rejected` Contestado, `refunded` Devolvido). Filtro `status` (ex.: `open,analysis`) e páginas por `cursor`.
     *
     * **Permissão da chave:** `meds:list`.
     *
     * `GET /v1/meds`
     *
     * Campos:
     * - `limit`: Quantos por página (até 100).
     * - `cursor`: O `nextCursor` da página anterior.
     * - `status`: Uma ou mais situações separadas por vírgula: `open`, `analysis`, `rejected`, `refunded` (ex.: `open,analysis`).
     *
     * @param array{
     *     limit?: float|int,
     *     cursor?: string,
     *     status?: string
     * } $params
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return \WyzeCash\Page<array{
     *     id: string,
     *     original: array{
     *         id: string,
     *         payerName: string|null,
     *         institution: string|null
     *     },
     *     status: string,
     *     statusTitle: string,
     *     amount: int,
     *     blocked: int,
     *     owed: int,
     *     fee: int,
     *     receivedFee: int,
     *     receivedFeeOwed: int,
     *     reason: string,
     *     reasonTitle: string,
     *     description: string|null,
     *     deadline: string,
     *     defense: string|null,
     *     answered: string|null,
     *     answeredAt: string|null,
     *     defenseSource: string|null,
     *     attachments: list<array{
     *         id: string,
     *         name: string,
     *         size: int,
     *         contentType: string,
     *         createdAt: string
     *     }>,
     *     decisionNote: string|null,
     *     defenseAt: string|null,
     *     decidedAt: string|null,
     *     createdAt: string
     * }>
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function list(array $params = [], array $options = []): \WyzeCash\Page
    {
        return $this->client->list('listar_meds', [], $params, $options);
    }

    /**
     * Consultar MED
     *
     * Um MED pelo código MED-, com a cobrança de origem e quem respondeu (`answered`: `user`, `auto` ou `null`).
     *
     * **Permissão da chave:** `meds:get`.
     *
     * `GET /v1/meds/{id}`
     *
     * @param string $id O código.
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     id: string,
     *     original: array{
     *         id: string,
     *         payerName: string|null,
     *         institution: string|null
     *     },
     *     status: string,
     *     statusTitle: string,
     *     amount: int,
     *     blocked: int,
     *     owed: int,
     *     fee: int,
     *     receivedFee: int,
     *     receivedFeeOwed: int,
     *     reason: string,
     *     reasonTitle: string,
     *     description: string|null,
     *     deadline: string,
     *     defense: string|null,
     *     answered: string|null,
     *     answeredAt: string|null,
     *     defenseSource: string|null,
     *     attachments: list<array{
     *         id: string,
     *         name: string,
     *         size: int,
     *         contentType: string,
     *         createdAt: string
     *     }>,
     *     decisionNote: string|null,
     *     defenseAt: string|null,
     *     decidedAt: string|null,
     *     createdAt: string
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function retrieve(string $id, array $options = []): array
    {
        return $this->client->request('consultar_med', [$id], [], $options);
    }

    /**
     * Responder MED
     *
     * Manda a defesa de um MED aberto (`defense`, de 20 a 4.000 caracteres, explicando a venda e a entrega) com até 5 provas em `evidence` (imagem JPG, PNG ou WEBP ou PDF, em base64 em `contentBase64`, até 5 MB cada). A WyzeCash leva a defesa e as provas ao banco de quem pagou; depois disso o MED segue a situação de sempre.
     *
     * **Permissão da chave:** `meds:respond`.
     *
     * - Só com o MED `open` e dentro do prazo (`deadline`): depois, `409 MED_NOT_OPEN` ou `409 MED_DEADLINE_PASSED`. Uma resposta por MED.
     * - As provas não voltam para baixar: ficam guardadas com a WyzeCash e só vão para o banco de quem pagou.
     * - O MCP não manda a defesa: só mostra como o seu sistema faz.
     *
     * `POST /v1/meds/{id}/respond`
     *
     * Campos:
     * - `defense`: A defesa que você escreveu (a automática nunca aparece).
     *
     * @param string $id O código.
     * @param array{
     *     defense: string,
     *     evidence?: list<array{
     *         fileName?: string,
     *         contentBase64: string
     *     }>
     * } $params
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     id: string,
     *     original: mixed,
     *     status: string,
     *     statusTitle: string,
     *     amount: int,
     *     blocked: int,
     *     owed: int,
     *     fee: int,
     *     receivedFee: int,
     *     receivedFeeOwed: int,
     *     reason: string,
     *     reasonTitle: string,
     *     description: string|null,
     *     deadline: string,
     *     defense: string,
     *     answered: string,
     *     answeredAt: string,
     *     defenseSource: string,
     *     attachments: list<array{
     *         id: string,
     *         name: string,
     *         size: int,
     *         contentType: string,
     *         createdAt: string
     *     }>,
     *     decisionNote: string|null,
     *     defenseAt: string,
     *     decidedAt: mixed,
     *     createdAt: string
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function respond(string $id, array $params, array $options = []): array
    {
        return $this->client->request('responder_med', [$id], $params, $options);
    }
}
