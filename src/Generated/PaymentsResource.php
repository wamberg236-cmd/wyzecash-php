<?php

// Gerado por scripts/generate.php a partir de ../openapi.json. Não edite à mão.

declare(strict_types=1);

namespace WyzeCash\Generated;

use WyzeCash\Requester;

final class PaymentsResource
{
    public function __construct(private readonly Requester $client)
    {
    }

    /**
     * Criar cobrança
     *
     * Cria uma cobrança (BKY-) no Pix, no cartão ou em cripto. Valores em centavos.
     *
     * **Permissão da chave:** `payments:create` ou `payments:create-card` ou `payments:create-crypto`.
     *
     * Pede o cabeçalho `Idempotency-Key`.
     *
     * - `method`: `pix` (padrão), `card` ou `crypto`. Cada forma pede a sua permissão: `payments:create` (Pix), `payments:create-card` (cartão), `payments:create-crypto` (cripto).
     * - Pix: volta o `copyPaste` (Pix Copia e Cola) e vale sempre 30 minutos (`expiresAt` diz até quando). `expiresInMinutes` é aceito e ignorado.
     * - Cartão (à vista, a partir de R$ 5,00): volta o `paymentUrl`, a página de pagamento, que vale 30 minutos. Venda no cartão fica "A liberar" e entra no disponível depois do prazo da conta.
     * - Cripto (a partir de R$ 2,00): `cryptoCurrency` (`ltc` padrão, `btc`, `eth`, `sol`, `trx`, `doge`, `ada`, `matic`); volta `crypto` com moeda, valor e endereço; vale 10 minutos.
     * - `feePaidBy`: `payer` (quem paga paga o valor + a tarifa e você recebe o valor inteiro) ou `user` (a tarifa sai do valor). Sem ele, vale o padrão da conta.
     * - A resposta traz `amount` (o que você pediu), `total` (o que quem paga paga), `fee`, `netAmount` (o que você recebe) e `feePaidBy`.
     * - `customerId` liga ao cliente; `subaccountId` credita a venda numa subconta; `split` divide entre até 10 subcontas.
     * - Para saber quando foi paga, use o webhook `payment.paid` em vez de consultar.
     *
     * `POST /v1/payments`
     *
     * Campos:
     * - `amount`: Valor, em centavos.
     * - `description`: Descrição da cobrança (aparece para quem paga e no extrato).
     * - `expiresInMinutes`: Aceito e ignorado: o Pix vale sempre 30 minutos.
     * - `subaccountId`: Subconta que recebe a venda (o `sacc_` ou o `externalId`).
     * - `split`: Divide a venda entre até 10 subcontas.
     * - `customerId`: Código do cliente.
     * - `method`: Forma: `pix`, `card` ou `crypto`.
     * - `cryptoCurrency`: Moeda da cripto: `ltc` (padrão), `btc`, `eth`, `sol`, `trx`, `doge`, `ada` ou `matic`.
     * - `feePaidBy`: Quem paga a tarifa: `payer` (quem paga paga o valor + a tarifa e você recebe o valor inteiro) ou `user` (a tarifa sai do valor). Sem ele, vale o padrão da conta.
     * - `tracking`: Dados de rastreio da venda (UTM e pixels), repassados às integrações.
     *
     * @param array{
     *     amount: int,
     *     description?: string,
     *     expiresInMinutes?: int,
     *     subaccountId?: string,
     *     split?: list<array{
     *         subaccountId: string,
     *         amount: int
     *     }>,
     *     customerId?: string,
     *     method?: 'pix'|'card'|'crypto',
     *     cryptoCurrency?: 'ltc'|'btc'|'eth'|'sol'|'trx'|'doge'|'ada'|'matic',
     *     feePaidBy?: 'payer'|'user',
     *     tracking?: array{
     *         utm_source?: string,
     *         utm_medium?: string,
     *         utm_campaign?: string,
     *         utm_content?: string,
     *         utm_term?: string,
     *         src?: string,
     *         sck?: string,
     *         fbc?: string,
     *         fbp?: string,
     *         gaClientId?: string,
     *         gaSessionId?: string,
     *         ip?: string,
     *         userAgent?: string,
     *         pageUrl?: string
     *     }
     * } $params
     * @param array{idempotencyKey?: string, timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     id: string,
     *     status: string,
     *     statusTitle: string,
     *     amount: int,
     *     total: int,
     *     fee: int,
     *     netAmount: int,
     *     feePaidBy: string,
     *     refundedAmount: int,
     *     description: string|null,
     *     txid: string,
     *     method: string,
     *     methodTitle: string,
     *     copyPaste: string|null,
     *     paymentUrl: string|null,
     *     crypto: array{
     *         currency: string,
     *         currencyTitle: string,
     *         amount: string,
     *         address: string
     *     }|null,
     *     card: array{
     *         brand: string,
     *         last4: string
     *     }|null,
     *     availableAt: string|null,
     *     released: bool|null,
     *     expiresAt: string,
     *     paidAt: string|null,
     *     customer: array{
     *         id: string,
     *         name: string
     *     }|null,
     *     subaccountId: string|null,
     *     split: list<array{
     *         subaccountId: string,
     *         name: string,
     *         amount: int
     *     }>|null,
     *     payer: array{
     *         name: string,
     *         document: string|null,
     *         institution: string|null
     *     }|null,
     *     createdAt: string
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function create(array $params, array $options = []): array
    {
        return $this->client->request('criar_cobranca', [], $params, $options);
    }

    /**
     * Listar cobranças
     *
     * As cobranças da conta, das mais novas para as mais antigas, com cursor (`limit` até 100 e o `cursor` da página anterior). `subaccountId` (o `sacc_` ou o `externalId`) traz só as de uma subconta, inclusive as divididas com ela. Filtros: `status` (`pending`, `paid`, `expired`, `refunded`), `method` (`pix`, `card`, `crypto`), `customerId` e `q` (busca no código, na descrição, em quem pagou e no nome do cliente).
     *
     * **Permissão da chave:** `payments:list`.
     *
     * `GET /v1/payments`
     *
     * Campos:
     * - `limit`: Quantos por página (até 100).
     * - `cursor`: O `nextCursor` da página anterior.
     * - `subaccountId`: Só as cobranças de uma subconta (o `sacc_` ou o `externalId`), inclusive as divididas com ela.
     * - `customerId`: Só as cobranças deste cliente.
     * - `status`: Só as cobranças nesta situação: `pending` (Entrada pendente), `paid` (paga), `expired` (Entrada expirada) ou `refunded` (estornada).
     * - `method`: Só as cobranças desta forma: `pix`, `card` ou `crypto`.
     * - `q`: Busca no código, na descrição, em quem pagou e no nome do cliente.
     *
     * @param array{
     *     limit?: float|int,
     *     cursor?: string,
     *     subaccountId?: string,
     *     customerId?: string,
     *     status?: 'pending'|'paid'|'expired'|'refunded',
     *     method?: 'pix'|'card'|'crypto',
     *     q?: string
     * } $params
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return \WyzeCash\Page<array{
     *     id: string,
     *     status: string,
     *     statusTitle: string,
     *     amount: int,
     *     total: int,
     *     fee: int,
     *     netAmount: int,
     *     feePaidBy: string,
     *     refundedAmount: int,
     *     description: string|null,
     *     txid: string,
     *     method: string,
     *     methodTitle: string,
     *     copyPaste: string|null,
     *     paymentUrl: string|null,
     *     crypto: array{
     *         currency: string,
     *         currencyTitle: string,
     *         amount: string,
     *         address: string
     *     }|null,
     *     card: array{
     *         brand: string,
     *         last4: string
     *     }|null,
     *     availableAt: string|null,
     *     released: bool|null,
     *     expiresAt: string,
     *     paidAt: string|null,
     *     customer: array{
     *         id: string,
     *         name: string
     *     }|null,
     *     subaccountId: string|null,
     *     split: list<array{
     *         subaccountId: string,
     *         name: string,
     *         amount: int
     *     }>|null,
     *     payer: array{
     *         name: string,
     *         document: string,
     *         institution: string
     *     }|null,
     *     createdAt: string
     * }>
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function list(array $params = [], array $options = []): \WyzeCash\Page
    {
        return $this->client->list('listar_cobrancas', [], $params, $options);
    }

    /**
     * Formas de pagamento disponíveis
     *
     * As formas que dá para cobrar agora (só as que a chave pode criar), com o valor mínimo e o máximo de cada uma, em centavos.
     *
     * **Permissão da chave:** `payments:create` ou `payments:create-card` ou `payments:create-crypto`.
     *
     * `GET /v1/payments/methods`
     *
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     items: list<array{
     *         method: string,
     *         title: string,
     *         available: bool,
     *         minAmount: int,
     *         maxAmount: int|null,
     *         installments?: int,
     *         holdDays?: int,
     *         currencies?: list<array{
     *             id: string,
     *             title: string
     *         }>
     *     }>
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function methods(array $options = []): array
    {
        return $this->client->request('formas_de_pagamento', [], [], $options);
    }

    /**
     * Consultar cobrança
     *
     * Uma cobrança pelo código BKY-: situação (`pending`, `paid`, `expired`, `refunded`), valores e, paga no Pix, quem pagou (`payer`, com o CPF mascarado).
     *
     * **Permissão da chave:** `payments:get`.
     *
     * `GET /v1/payments/{id}`
     *
     * @param string $id O código.
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     id: string,
     *     status: string,
     *     statusTitle: string,
     *     amount: int,
     *     total: int,
     *     fee: int,
     *     netAmount: int,
     *     feePaidBy: string,
     *     refundedAmount: int,
     *     description: string|null,
     *     txid: string,
     *     method: string,
     *     methodTitle: string,
     *     copyPaste: string|null,
     *     paymentUrl: string|null,
     *     crypto: array{
     *         currency: string,
     *         currencyTitle: string,
     *         amount: string,
     *         address: string
     *     }|null,
     *     card: array{
     *         brand: string,
     *         last4: string
     *     }|null,
     *     availableAt: string|null,
     *     released: bool|null,
     *     expiresAt: string,
     *     paidAt: string|null,
     *     customer: array{
     *         id: string,
     *         name: string
     *     }|null,
     *     subaccountId: string|null,
     *     split: list<array{
     *         subaccountId: string,
     *         name: string,
     *         amount: int
     *     }>|null,
     *     payer: array{
     *         name: string,
     *         document: string|null,
     *         institution: string|null
     *     }|null,
     *     createdAt: string
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function retrieve(string $id, array $options = []): array
    {
        return $this->client->request('consultar_cobranca', [$id], [], $options);
    }
}
