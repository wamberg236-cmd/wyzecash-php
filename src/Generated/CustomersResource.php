<?php

// Gerado por scripts/generate.php a partir de ../openapi.json. Não edite à mão.

declare(strict_types=1);

namespace WyzeCash\Generated;

use WyzeCash\Requester;

final class CustomersResource
{
    public function __construct(private readonly Requester $client)
    {
    }

    /**
     * Listar clientes
     *
     * Os clientes da conta.
     *
     * **Permissão da chave:** `customers:list`.
     *
     * `GET /v1/customers`
     *
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return list<array{
     *     id: string,
     *     name: string,
     *     email: string|null,
     *     phone: string|null,
     *     document: string|null,
     *     createdAt: string,
     *     stats: array{
     *         paid: int,
     *         open: int,
     *         count: int
     *     }
     * }>
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function list(array $options = []): array
    {
        return $this->client->request('listar_clientes', [], [], $options);
    }

    /**
     * Criar cliente
     *
     * Cadastra um cliente (e-mail, celular e CPF/CNPJ ficam criptografados). O `id` vai no `customerId` da cobrança.
     *
     * **Permissão da chave:** `customers:create`.
     *
     * `POST /v1/customers`
     *
     * Campos:
     * - `name`: Nome do cliente.
     * - `email`: E-mail.
     * - `phone`: Celular.
     * - `document`: CPF ou CNPJ do cliente.
     *
     * @param array{
     *     name: string,
     *     email?: string|null,
     *     phone?: string|null,
     *     document?: string|null
     * } $params
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     id: string,
     *     name: string,
     *     email: string|null,
     *     phone: string|null,
     *     document: string|null,
     *     createdAt: string
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function create(array $params, array $options = []): array
    {
        return $this->client->request('criar_cliente', [], $params, $options);
    }

    /**
     * Consultar cliente
     *
     * Um cliente pelo id.
     *
     * **Permissão da chave:** `customers:get`.
     *
     * `GET /v1/customers/{id}`
     *
     * @param string $id O código.
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     id: string,
     *     name: string,
     *     email: string|null,
     *     phone: string|null,
     *     document: string|null,
     *     createdAt: string
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function retrieve(string $id, array $options = []): array
    {
        return $this->client->request('consultar_cliente', [$id], [], $options);
    }

    /**
     * Editar cliente
     *
     * Muda os dados de um cliente.
     *
     * **Permissão da chave:** `customers:update`.
     *
     * `PATCH /v1/customers/{id}`
     *
     * Campos:
     * - `name`: Nome do cliente.
     * - `email`: E-mail.
     * - `phone`: Celular.
     * - `document`: CPF ou CNPJ do cliente. `null` apaga.
     *
     * @param string $id O código.
     * @param array{
     *     name?: string,
     *     email?: string|null,
     *     phone?: string|null,
     *     document?: string|null
     * } $params
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     id: string,
     *     name: string,
     *     email: string,
     *     phone: string|null,
     *     document: string,
     *     createdAt: string
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function update(string $id, array $params = [], array $options = []): array
    {
        return $this->client->request('editar_cliente', [$id], $params, $options);
    }

    /**
     * Excluir cliente
     *
     * Esconde o cliente; as cobranças antigas mantêm o nome.
     *
     * **Permissão da chave:** `customers:delete`.
     *
     * `DELETE /v1/customers/{id}`
     *
     * @param string $id O código.
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     removed: bool
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function delete(string $id, array $options = []): array
    {
        return $this->client->request('excluir_cliente', [$id], [], $options);
    }
}
