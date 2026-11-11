<?php

// Gerado por scripts/generate.php a partir de ../openapi.json. Não edite à mão.

declare(strict_types=1);

namespace WyzeCash\Generated;

use WyzeCash\Requester;

final class PaymentLinksResource
{
    public function __construct(private readonly Requester $client)
    {
    }

    /**
     * Criar link de pagamento
     *
     * Cria um link de pagamento (LNK-): uma página pública em que cada pagador gera a própria cobrança.
     *
     * **Permissão da chave:** `payment-links:create`.
     *
     * - `amount` fixo, ou sem `amount` para valores avulsos (`suggestedAmounts`, até 6, e `allowCustomAmount` com `minAmount`/`maxAmount`).
     * - `methods`: `pix`, `card`, `crypto` (cartão e cripto pedem também `payments:create-card` ou `payments:create-crypto`).
     * - `customerFields`: quais dados pedir (`name`, `email`, `phone`, `document`); `maxPayments` e `expiresAt` limitam o link.
     * - `feePaidBy: "payer"`: quem paga o link paga o valor + a tarifa.
     *
     * `POST /v1/payment-links`
     *
     * Campos:
     * - `title`: Título.
     * - `description`: Texto que aparece na página do link.
     * - `amount`: Valor fixo em centavos. Sem ele, quem paga escolhe entre `minAmount` e `maxAmount`.
     * - `minAmount`: Menor valor, em centavos.
     * - `maxAmount`: Maior valor, em centavos.
     * - `suggestedAmounts`: Valores sugeridos na página, em centavos.
     * - `allowCustomAmount`: Quem paga pode digitar outro valor.
     * - `displayName`: Nome que aparece na página do link.
     * - `accentColor`: Cor da página (hex, ex.: `#0a7cff`).
     * - `pageTheme`: Tema da página: `auto`, `light` ou `dark`.
     * - `buttonLabel`: Texto do botão de pagar.
     * - `thankYouMessage`: Mensagem depois do pagamento.
     * - `successUrl`: Para onde levar quem pagou (https).
     * - `collectCustomer`: Pede os dados de quem paga.
     * - `customerFields`: Quais dados pedir de quem paga.
     * - `maxPayments`: Máximo de pagamentos (depois disso o link esgota).
     * - `expiresAt`: Até quando o link aceita pagamento (data e hora ISO 8601).
     * - `active`: O link aceita pagamento (padrão `true`).
     * - `methods`: As formas aceitas no link: `pix`, `card`, `crypto`.
     * - `feePaidBy`: Quem paga a tarifa: `payer` (quem paga paga o valor + a tarifa e você recebe o valor inteiro) ou `user` (a tarifa sai do valor). Sem ele, vale o padrão da conta.
     * - `subaccountId`: Credita as vendas do link nesta subconta (o `sacc_` ou o `externalId`).
     *
     * @param array{
     *     title: string,
     *     description?: string|null,
     *     amount?: int|null,
     *     minAmount?: int,
     *     maxAmount?: int|null,
     *     suggestedAmounts?: list<int>|null,
     *     allowCustomAmount?: bool,
     *     displayName?: string|null,
     *     accentColor?: string|null,
     *     pageTheme?: 'auto'|'light'|'dark',
     *     buttonLabel?: string|null,
     *     thankYouMessage?: string|null,
     *     successUrl?: string|null,
     *     collectCustomer?: bool,
     *     customerFields?: list<'name'|'email'|'phone'|'document'>,
     *     maxPayments?: int|null,
     *     expiresAt?: string|null,
     *     active?: bool,
     *     methods?: list<'pix'|'card'|'crypto'>,
     *     feePaidBy?: 'payer'|'user',
     *     subaccountId?: string|null
     * } $params
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     id: string,
     *     title: string,
     *     description: string|null,
     *     amount: int|null,
     *     minAmount: int,
     *     maxAmount: int|null,
     *     suggestedAmounts: list<int>,
     *     allowCustomAmount: bool,
     *     collectCustomer: bool,
     *     customerFields: list<string>,
     *     methods: list<string>,
     *     feePaidBy: string,
     *     subaccount: array{
     *         id: string,
     *         name: string
     *     }|null,
     *     displayName: string|null,
     *     accentColor: string|null,
     *     pageTheme: string,
     *     buttonLabel: string|null,
     *     thankYouMessage: string|null,
     *     successUrl: string|null,
     *     maxPayments: int|null,
     *     expiresAt: string|null,
     *     active: bool,
     *     status: string,
     *     statusTitle: string,
     *     payments: int,
     *     received: int,
     *     lastPaidAt: string|null,
     *     createdAt: string
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function create(array $params, array $options = []): array
    {
        return $this->client->request('criar_link', [], $params, $options);
    }

    /**
     * Listar links de pagamento
     *
     * Os links da conta com a situação (`active`, `paused`, `expired`, `sold_out`), quantas vendas e quanto recebeu.
     *
     * **Permissão da chave:** `payment-links:list`.
     *
     * `GET /v1/payment-links`
     *
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     items: list<array{
     *         id: string,
     *         title: string,
     *         description: string|null,
     *         amount: int|null,
     *         minAmount: int,
     *         maxAmount: int|null,
     *         suggestedAmounts: list<int>,
     *         allowCustomAmount: bool,
     *         collectCustomer: bool,
     *         customerFields: list<string>,
     *         methods: list<string>,
     *         feePaidBy: string,
     *         subaccount: array{
     *             id: string,
     *             name: string
     *         }|null,
     *         displayName: string|null,
     *         accentColor: string|null,
     *         pageTheme: string,
     *         buttonLabel: string|null,
     *         thankYouMessage: string|null,
     *         successUrl: string|null,
     *         maxPayments: int|null,
     *         expiresAt: string|null,
     *         active: bool,
     *         status: string,
     *         statusTitle: string,
     *         payments: int,
     *         received: int,
     *         lastPaidAt: string|null,
     *         createdAt: string
     *     }>
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function list(array $options = []): array
    {
        return $this->client->request('listar_links', [], [], $options);
    }

    /**
     * Consultar link de pagamento
     *
     * Um link pelo código LNK-.
     *
     * **Permissão da chave:** `payment-links:get`.
     *
     * `GET /v1/payment-links/{id}`
     *
     * @param string $id O código.
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     id: string,
     *     title: string,
     *     description: string,
     *     amount: int,
     *     minAmount: int,
     *     maxAmount: int|null,
     *     suggestedAmounts: list<int>,
     *     allowCustomAmount: bool,
     *     collectCustomer: bool,
     *     customerFields: list<string>,
     *     methods: list<string>,
     *     feePaidBy: string,
     *     subaccount: array{
     *         id: string,
     *         name: string
     *     }|null,
     *     displayName: string|null,
     *     accentColor: string|null,
     *     pageTheme: string,
     *     buttonLabel: string|null,
     *     thankYouMessage: string|null,
     *     successUrl: string|null,
     *     maxPayments: int,
     *     expiresAt: string|null,
     *     active: bool,
     *     status: string,
     *     statusTitle: string,
     *     payments: int,
     *     received: int,
     *     lastPaidAt: string,
     *     createdAt: string
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function retrieve(string $id, array $options = []): array
    {
        return $this->client->request('consultar_link', [$id], [], $options);
    }

    /**
     * Editar link de pagamento
     *
     * Muda os dados do link. `active: false` pausa.
     *
     * **Permissão da chave:** `payment-links:update`.
     *
     * `PATCH /v1/payment-links/{id}`
     *
     * Campos:
     * - `title`: Título.
     * - `description`: Descrição.
     * - `amount`: Valor fixo em centavos. `null` deixa quem paga escolher.
     * - `minAmount`: Menor valor, em centavos.
     * - `maxAmount`: Maior valor, em centavos.
     * - `suggestedAmounts`: Valores sugeridos na página, em centavos.
     * - `allowCustomAmount`: Quem paga pode digitar outro valor.
     * - `displayName`: Nome que aparece na página do link.
     * - `accentColor`: Cor da página (hex, ex.: `#0a7cff`).
     * - `pageTheme`: Tema da página: `auto`, `light` ou `dark`.
     * - `buttonLabel`: Texto do botão de pagar.
     * - `thankYouMessage`: Mensagem depois do pagamento.
     * - `successUrl`: Para onde levar quem pagou (https).
     * - `collectCustomer`: Pede os dados de quem paga.
     * - `customerFields`: Quais dados pedir de quem paga.
     * - `maxPayments`: Máximo de pagamentos (depois disso o link esgota).
     * - `expiresAt`: Até quando o link aceita pagamento (data e hora ISO 8601). `null` tira o prazo.
     * - `active`: `false` pausa o link; `true` volta a aceitar.
     * - `methods`: As formas aceitas no link: `pix`, `card`, `crypto`.
     * - `feePaidBy`: Quem paga a tarifa: `payer` (quem paga paga o valor + a tarifa e você recebe o valor inteiro) ou `user` (a tarifa sai do valor). Sem ele, vale o padrão da conta.
     * - `subaccountId`: Credita as vendas do link nesta subconta. `null` tira.
     *
     * @param string $id O código.
     * @param array{
     *     title?: string,
     *     description?: string|null,
     *     amount?: int|null,
     *     minAmount?: int,
     *     maxAmount?: int|null,
     *     suggestedAmounts?: list<int>|null,
     *     allowCustomAmount?: bool,
     *     displayName?: string|null,
     *     accentColor?: string|null,
     *     pageTheme?: 'auto'|'light'|'dark',
     *     buttonLabel?: string|null,
     *     thankYouMessage?: string|null,
     *     successUrl?: string|null,
     *     collectCustomer?: bool,
     *     customerFields?: list<'name'|'email'|'phone'|'document'>,
     *     maxPayments?: int|null,
     *     expiresAt?: string|null,
     *     active?: bool,
     *     methods?: list<'pix'|'card'|'crypto'>,
     *     feePaidBy?: 'payer'|'user',
     *     subaccountId?: string|null
     * } $params
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     id: string,
     *     title: string,
     *     description: string|null,
     *     amount: int|null,
     *     minAmount: int,
     *     maxAmount: int|null,
     *     suggestedAmounts: list<int>,
     *     allowCustomAmount: bool,
     *     collectCustomer: bool,
     *     customerFields: list<string>,
     *     methods: list<string>,
     *     feePaidBy: string,
     *     subaccount: array{
     *         id: string,
     *         name: string
     *     }|null,
     *     displayName: string|null,
     *     accentColor: string|null,
     *     pageTheme: string,
     *     buttonLabel: string|null,
     *     thankYouMessage: string|null,
     *     successUrl: string|null,
     *     maxPayments: int|null,
     *     expiresAt: string|null,
     *     active: bool,
     *     status: string,
     *     statusTitle: string,
     *     payments: int,
     *     received: int,
     *     lastPaidAt: string|null,
     *     createdAt: string
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function update(string $id, array $params = [], array $options = []): array
    {
        return $this->client->request('editar_link', [$id], $params, $options);
    }

    /**
     * Excluir link de pagamento
     *
     * O link para de funcionar; as cobranças já geradas ficam.
     *
     * **Permissão da chave:** `payment-links:delete`.
     *
     * `DELETE /v1/payment-links/{id}`
     *
     * @param string $id O código.
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     ok: bool
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function delete(string $id, array $options = []): array
    {
        return $this->client->request('excluir_link', [$id], [], $options);
    }

    /**
     * Cobranças geradas por um link
     *
     * As cobranças BKY- que os pagadores geraram pelo link.
     *
     * **Permissão da chave:** `payment-links:payments`.
     *
     * `GET /v1/payment-links/{id}/payments`
     *
     * @param string $id O código.
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     items: list<array{
     *         id: string,
     *         amount: int,
     *         status: string,
     *         method: string,
     *         expiresAt: string,
     *         paidAt: string|null,
     *         createdAt: string,
     *         customerId: string|null,
     *         hasDetails: bool,
     *         statusTitle: string,
     *         bumps: list<array{
     *             id: string,
     *             title: string,
     *             amount: int
     *         }>,
     *         coupon: array{
     *             code: string,
     *             discount: int
     *         }|null
     *     }>
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function listPayments(string $id, array $options = []): array
    {
        return $this->client->request('cobrancas_do_link', [$id], [], $options);
    }
}
