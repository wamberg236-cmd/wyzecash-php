<?php

// Gerado por scripts/generate.php a partir de ../openapi.json. Não edite à mão.

declare(strict_types=1);

namespace WyzeCash\Generated;

use WyzeCash\Requester;

final class SubaccountsResource
{
    public function __construct(private readonly Requester $client)
    {
    }

    /**
     * Criar subconta
     *
     * Cria uma subconta (sacc_) para um cliente seu: o dinheiro fica no seu saldo e a subconta diz quanto é dele. Em toda rota vale o `sacc_` ou o seu `externalId`.
     *
     * **Permissão da chave:** `subaccounts:create`.
     *
     * - Sem taxa no pedido, a subconta segue a taxa global da Configuração das subcontas (`feeMode: "global"`); com comissão por venda (`saleFeeBps`, `saleFeeFixed`) ou taxa por saque (`withdrawFeeBps`, `withdrawFeeFixed`), fica com a própria (`feeMode: "custom"`). `pixKey` é a chave Pix do cliente para o saque dele.
     * - Os `limits` que não vierem no pedido usam os limites padrão da Configuração das subcontas.
     * - O `name` tem de 2 a 80 caracteres. Use só o nome do cliente: o CPF/CNPJ vai em `document` e o id do seu sistema em `externalId`.
     * - Opcionais: `document` (CPF ou CNPJ do titular, guardado criptografado; volta inteiro ao abrir a subconta e mascarado na lista), `lockMode` (`none`, `receive`, `withdraw` ou `full`) e `limits` (`perWithdrawal`, `daily`, `monthly` em centavos para o saque da subconta; `null` sem limite).
     *
     * `POST /v1/subaccounts`
     *
     * Campos:
     * - `name`: Nome da subconta (o titular).
     * - `email`: E-mail do cliente da subconta.
     * - `externalId`: O seu código para esta subconta (único na conta). Vale no lugar do `sacc_` em toda rota.
     * - `saleFeeBps`: Percentual que você cobra na venda, em centésimos de ponto (150 = 1,5%).
     * - `saleFeeFixed`: Valor fixo que você cobra por venda, em centavos.
     * - `withdrawFeeBps`: Percentual que você cobra no saque, em centésimos de ponto (150 = 1,5%).
     * - `withdrawFeeFixed`: Valor fixo que você cobra por saque, em centavos.
     * - `feeMode`: `global` (segue a taxa da Configuração das subcontas) ou `custom` (taxa própria).
     * - `metadata`: Dados seus, livres (chave e valor). Volta igual nas consultas.
     * - `pixKey`: Chave Pix guardada para o saque da subconta.
     * - `lockMode`: Bloqueio: `none`, `receive` (não recebe), `withdraw` (não saca) ou `full` (bloqueio total). Devolver à principal sempre vale.
     * - `limits`: Limites do saque da subconta, em centavos.
     * - `document`: CPF ou CNPJ do titular, inteiro (só números ou com pontuação). Guardado criptografado.
     *
     * @param array{
     *     name: string,
     *     email?: string|null,
     *     externalId?: string|null,
     *     saleFeeBps?: int,
     *     saleFeeFixed?: int,
     *     withdrawFeeBps?: int,
     *     withdrawFeeFixed?: int,
     *     feeMode?: 'global'|'custom',
     *     metadata?: array<string, mixed>|null,
     *     pixKey?: array{
     *         keyType: 'cpf'|'cnpj'|'email'|'phone'|'random',
     *         key: string
     *     }|null,
     *     lockMode?: 'none'|'receive'|'withdraw'|'full',
     *     limits?: array{
     *         perWithdrawal?: int|null,
     *         daily?: int|null,
     *         monthly?: int|null
     *     },
     *     document?: string|null
     * } $params
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     id: string,
     *     name: string,
     *     externalId: string|null,
     *     hasEmail: bool,
     *     feeMode: string,
     *     saleFee: array{
     *         percentBps: int,
     *         fixed: int
     *     },
     *     withdrawFee: array{
     *         percentBps: int,
     *         fixed: int
     *     },
     *     balance: int,
     *     blocked: int,
     *     available: int,
     *     active: bool,
     *     lockMode: string,
     *     medLocked: bool,
     *     medPending: int,
     *     limits: array{
     *         perWithdrawal: int|null,
     *         daily: int|null,
     *         monthly: int|null
     *     },
     *     document: string|null,
     *     pixKey: array{
     *         keyType: string,
     *         key: string,
     *         holderName: string,
     *         institution: string
     *     }|null,
     *     createdAt: string,
     *     email: string|null,
     *     metadata: array<string, mixed>|null,
     *     stats: array{
     *         sales: int,
     *         gross: int,
     *         received: int,
     *         fees: int,
     *         returned: int,
     *         meds: int,
     *         lastMovementAt: string|null
     *     }
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function create(array $params, array $options = []): array
    {
        return $this->client->request('criar_subconta', [], $params, $options);
    }

    /**
     * Listar subcontas
     *
     * As subcontas com o saldo de cada uma (`balance`, `blocked` reservado por MED ou estorno em andamento e `available` livre), as taxas que valem (`feeMode`, `saleFee`, `withdrawFee`), a pendência do MED (`medPending`, `medLocked`), os números (`stats`: vendas, recebido, taxas, devolvido, MED) e o total atribuído.
     *
     * **Permissão da chave:** `subaccounts:list`.
     *
     * `GET /v1/subaccounts`
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
     *     name: string,
     *     externalId: string|null,
     *     hasEmail: bool,
     *     feeMode: string,
     *     saleFee: array{
     *         percentBps: int,
     *         fixed: int
     *     },
     *     withdrawFee: array{
     *         percentBps: int,
     *         fixed: int
     *     },
     *     balance: int,
     *     blocked: int,
     *     available: int,
     *     active: bool,
     *     lockMode: string,
     *     medLocked: bool,
     *     medPending: int,
     *     limits: array{
     *         perWithdrawal: int|null,
     *         daily: int|null,
     *         monthly: int|null
     *     },
     *     document: string|null,
     *     pixKey: array{
     *         keyType: string,
     *         key: string,
     *         holderName: string,
     *         institution: string
     *     }|null,
     *     createdAt: string,
     *     stats: array{
     *         sales: int,
     *         gross: int,
     *         received: int,
     *         fees: int,
     *         returned: int,
     *         meds: int,
     *         lastMovementAt: string|null
     *     }
     * }>
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function list(array $params = [], array $options = []): \WyzeCash\Page
    {
        return $this->client->list('listar_subcontas', [], $params, $options);
    }

    /**
     * Consultar subconta
     *
     * Uma subconta pelo `sacc_` ou pelo `externalId`.
     *
     * **Permissão da chave:** `subaccounts:get`.
     *
     * `GET /v1/subaccounts/{id}`
     *
     * @param string $id O código.
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     id?: string,
     *     name?: string,
     *     externalId?: string|null,
     *     hasEmail?: bool,
     *     feeMode?: string,
     *     saleFee?: array{
     *         percentBps: int,
     *         fixed: int
     *     },
     *     withdrawFee?: array{
     *         percentBps: int,
     *         fixed: int
     *     },
     *     balance?: int,
     *     blocked?: int,
     *     available?: int,
     *     active?: bool,
     *     lockMode?: string,
     *     medLocked?: bool,
     *     medPending?: int,
     *     limits?: array{
     *         perWithdrawal: int|null,
     *         daily: int|null,
     *         monthly: int|null
     *     },
     *     document?: string|null,
     *     pixKey?: array{
     *         keyType: string,
     *         key: string,
     *         holderName: string,
     *         institution: string
     *     }|null,
     *     createdAt?: string,
     *     email?: string|null,
     *     metadata?: array<string, mixed>|null,
     *     stats?: array{
     *         sales: int,
     *         gross: int,
     *         received: int,
     *         fees: int,
     *         returned: int,
     *         meds: int,
     *         lastMovementAt: string|null
     *     },
     *     period?: array{
     *         from: string,
     *         to: string
     *     },
     *     sales?: array{
     *         count: int,
     *         total: int
     *     },
     *     main?: array{
     *         total: int,
     *         ownSales: array{
     *             count: int,
     *             total: int
     *         },
     *         subaccountShare: int
     *     },
     *     subaccounts?: array{
     *         count: int,
     *         total: int
     *     },
     *     returned?: array{
     *         count: int,
     *         total: int
     *     }
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function retrieve(string $id, array $options = []): array
    {
        return $this->client->request('consultar_subconta', [$id], [], $options);
    }

    /**
     * Editar subconta
     *
     * Muda os dados, a comissão (ou volta para a taxa global com `feeMode: "global"`), a chave Pix do cliente, o CPF/CNPJ do titular (`document`), o bloqueio parcial (`lockMode`: `receive` não recebe, `withdraw` não saca nem transfere, `full` os dois), os limites do saque (`limits`) ou ativa/desativa (`active`).
     *
     * **Permissão da chave:** `subaccounts:update`.
     *
     * `PATCH /v1/subaccounts/{id}`
     *
     * Campos:
     * - `name`: Nome da subconta (o titular).
     * - `email`: E-mail do cliente da subconta. `null` apaga.
     * - `externalId`: O seu código para esta subconta (único na conta). Vale no lugar do `sacc_` em toda rota.
     * - `saleFeeBps`: Percentual que você cobra na venda, em centésimos de ponto (150 = 1,5%).
     * - `saleFeeFixed`: Valor fixo que você cobra por venda, em centavos.
     * - `withdrawFeeBps`: Percentual que você cobra no saque, em centésimos de ponto (150 = 1,5%).
     * - `withdrawFeeFixed`: Valor fixo que você cobra por saque, em centavos.
     * - `feeMode`: `global` (segue a taxa da Configuração das subcontas) ou `custom` (taxa própria).
     * - `metadata`: Dados seus, livres (chave e valor). Volta igual nas consultas.
     * - `pixKey`: Chave Pix guardada para o saque da subconta.
     * - `lockMode`: Bloqueio: `none`, `receive` (não recebe), `withdraw` (não saca) ou `full` (bloqueio total). Devolver à principal sempre vale.
     * - `limits`: Limites do saque da subconta, em centavos.
     * - `document`: CPF ou CNPJ do titular, inteiro. `null` apaga.
     * - `active`: `false` desativa a subconta (não recebe, não envia e não saca).
     *
     * @param string $id O código.
     * @param array{
     *     name?: string,
     *     email?: string|null,
     *     externalId?: string|null,
     *     saleFeeBps?: int,
     *     saleFeeFixed?: int,
     *     withdrawFeeBps?: int,
     *     withdrawFeeFixed?: int,
     *     feeMode?: 'global'|'custom',
     *     metadata?: array<string, mixed>|null,
     *     pixKey?: array{
     *         keyType: 'cpf'|'cnpj'|'email'|'phone'|'random',
     *         key: string
     *     }|null,
     *     lockMode?: 'none'|'receive'|'withdraw'|'full',
     *     limits?: array{
     *         perWithdrawal?: int|null,
     *         daily?: int|null,
     *         monthly?: int|null
     *     },
     *     document?: string|null,
     *     active?: bool
     * } $params
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     id: string,
     *     name: string,
     *     externalId: string|null,
     *     hasEmail: bool,
     *     feeMode: string,
     *     saleFee: array{
     *         percentBps: int,
     *         fixed: int
     *     },
     *     withdrawFee: array{
     *         percentBps: int,
     *         fixed: int
     *     },
     *     balance: int,
     *     blocked: int,
     *     available: int,
     *     active: bool,
     *     lockMode: string,
     *     medLocked: bool,
     *     medPending: int,
     *     limits: array{
     *         perWithdrawal: int|null,
     *         daily: int|null,
     *         monthly: int|null
     *     },
     *     document: string|null,
     *     pixKey: array{
     *         keyType: string,
     *         key: string,
     *         holderName: string,
     *         institution: string
     *     }|null,
     *     createdAt: string,
     *     email: string|null,
     *     metadata: array<string, mixed>|null,
     *     stats: array{
     *         sales: int,
     *         gross: int,
     *         received: int,
     *         fees: int,
     *         returned: int,
     *         meds: int,
     *         lastMovementAt: string|null
     *     }
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function update(string $id, array $params = [], array $options = []): array
    {
        return $this->client->request('editar_subconta', [$id], $params, $options);
    }

    /**
     * Excluir subconta
     *
     * Exclui a subconta: o saldo dela volta à conta principal e o `externalId` fica livre.
     *
     * **Permissão da chave:** `subaccounts:delete`.
     *
     * `DELETE /v1/subaccounts/{id}`
     *
     * @param string $id O código.
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     deleted: bool,
     *     id: string,
     *     externalId: string,
     *     name: string,
     *     returned: int
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function delete(string $id, array $options = []): array
    {
        return $this->client->request('excluir_subconta', [$id], [], $options);
    }

    /**
     * Configuração das subcontas
     *
     * A taxa global (`saleFee`, `withdrawFee`: o que você ganha em cada venda e saque das subcontas com `feeMode` global), os limites padrão das subcontas novas (`limits`), o bloqueio automático por MED (`medAutoLock`), o saque para o titular (`holderWithdrawal`: `off`, `notify` ou `cpf_only`), quantas seguem a global (`subaccounts`) e as tarifas da WyzeCash na conta (`platform`). Centavos e pontos-base.
     *
     * **Permissão da chave:** `subaccounts:get`.
     *
     * `GET /v1/subaccounts/settings`
     *
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     saleFee: array{
     *         percentBps: int,
     *         fixed: int
     *     },
     *     withdrawFee: array{
     *         percentBps: int,
     *         fixed: int
     *     },
     *     limits: array{
     *         perWithdrawal: int|null,
     *         daily: int|null,
     *         monthly: int|null
     *     },
     *     medAutoLock: bool,
     *     holderWithdrawal: string,
     *     subaccounts: array{
     *         global: int,
     *         custom: int
     *     },
     *     platform: array{
     *         sale: array{
     *             percentBps: int,
     *             fixed: int
     *         },
     *         withdrawal: array{
     *             percentBps: int,
     *             fixed: int
     *         }
     *     },
     *     updatedAt: string|null
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function retrieveSettings(array $options = []): array
    {
        return $this->client->request('configuracao_subcontas', [], [], $options);
    }

    /**
     * Mudar a Configuração das subcontas
     *
     * Muda a taxa global (`saleFeeBps`, `saleFeeFixed`, `withdrawFeeBps`, `withdrawFeeFixed`), os limites padrão (`limits`, que não passam dos da conta), o bloqueio automático por MED (`medAutoLock`) e o saque para o titular (`holderWithdrawal`). O que não vier fica como está. Só o dono da conta.
     *
     * **Permissão da chave:** `subaccounts:update`.
     *
     * - Bloqueio automático por MED: MED numa venda da subconta sem saldo livre para reservar tudo tranca o saque dela (`medLocked`) e o que falta fica pendente (`medPending`); as próximas entradas dela reservam o que falta e, se o MED for devolvido, pagam a dívida. Sem pendência, ela destrava sozinha.
     * - Saque para o titular: `off` (padrão) saca para qualquer chave; `notify` também, e avisa no sino quando o banco confirma um CPF/CNPJ diferente do titular (o webhook traz `destination.holderMatch: "different"`); `cpf_only` só saca para a chave CPF/CNPJ igual ao `document` da subconta (outra chave: `422 SUBACCOUNT_HOLDER_KEY`; sem `document`: `422 SUBACCOUNT_HOLDER_MISSING`; sem chave no pedido, vai para ela).
     *
     * `PUT /v1/subaccounts/settings`
     *
     * Campos:
     * - `saleFeeBps`: Percentual que você cobra na venda, em centésimos de ponto (150 = 1,5%).
     * - `saleFeeFixed`: Valor fixo que você cobra por venda, em centavos.
     * - `withdrawFeeBps`: Percentual que você cobra no saque, em centésimos de ponto (150 = 1,5%).
     * - `withdrawFeeFixed`: Valor fixo que você cobra por saque, em centavos.
     * - `limits`: Limites padrão do saque das subcontas novas (até os da conta).
     * - `medAutoLock`: Bloqueio automático por MED: subconta sem saldo livre para o MED fica sem sacar até pagar.
     * - `holderWithdrawal`: Saque para o titular: `off`, `notify` (avisa quando é outra pessoa) ou `cpf_only` (só a chave CPF/CNPJ do titular).
     *
     * @param array{
     *     saleFeeBps?: int,
     *     saleFeeFixed?: int,
     *     withdrawFeeBps?: int,
     *     withdrawFeeFixed?: int,
     *     limits?: array{
     *         perWithdrawal?: int|null,
     *         daily?: int|null,
     *         monthly?: int|null
     *     },
     *     medAutoLock?: bool,
     *     holderWithdrawal?: 'off'|'notify'|'cpf_only'
     * } $params
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     saleFee: array{
     *         percentBps: int,
     *         fixed: int
     *     },
     *     withdrawFee: array{
     *         percentBps: int,
     *         fixed: int
     *     },
     *     limits: array{
     *         perWithdrawal: int|null,
     *         daily: int|null,
     *         monthly: int|null
     *     },
     *     medAutoLock: bool,
     *     holderWithdrawal: string,
     *     subaccounts: array{
     *         global: int,
     *         custom: int
     *     },
     *     platform: array{
     *         sale: array{
     *             percentBps: int,
     *             fixed: int
     *         },
     *         withdrawal: array{
     *             percentBps: int,
     *             fixed: int
     *         }
     *     },
     *     updatedAt: string
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function updateSettings(array $params = [], array $options = []): array
    {
        return $this->client->request('mudar_configuracao_subcontas', [], $params, $options);
    }

    /**
     * Exemplo da taxa das subcontas
     *
     * Quanto fica com você, com a subconta e com a WyzeCash numa venda de R$ 1,00 e de R$ 100,00 no Pix e num saque de R$ 100,00 da subconta, com a taxa informada e as tarifas da conta (`sales`, `withdrawal`).
     *
     * **Permissão da chave:** `subaccounts:get`.
     *
     * `GET /v1/subaccounts/settings/preview`
     *
     * Campos:
     * - `saleFeeBps`: Percentual que você cobra na venda, em centésimos de ponto (150 = 1,5%).
     * - `saleFeeFixed`: Valor fixo que você cobra por venda, em centavos.
     * - `withdrawFeeBps`: Percentual que você cobra no saque, em centésimos de ponto (150 = 1,5%).
     * - `withdrawFeeFixed`: Valor fixo que você cobra por saque, em centavos.
     *
     * @param array{
     *     saleFeeBps?: float|int,
     *     saleFeeFixed?: float|int,
     *     withdrawFeeBps?: float|int,
     *     withdrawFeeFixed?: float|int
     * } $params
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     sales: list<array{
     *         amount: int,
     *         platform: int,
     *         yours: int,
     *         subaccount: int
     *     }>,
     *     withdrawal: array{
     *         amount: int,
     *         platform: int,
     *         yours: int,
     *         pix: int
     *     }
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function previewSettings(array $params = [], array $options = []): array
    {
        return $this->client->request('exemplo_configuracao_subcontas', [], $params, $options);
    }

    /**
     * Movimentos da subconta
     *
     * Os movimentos SUB- da subconta; filtros `from`/`to` e `kind` (`sale`, `fund`, `transfer`, `withdraw`, `return`, `refund`, `med`).
     *
     * **Permissão da chave:** `subaccounts:entries`.
     *
     * `GET /v1/subaccounts/{id}/entries`
     *
     * Campos:
     * - `limit`: Quantos por página (até 100).
     * - `cursor`: O `nextCursor` da página anterior.
     * - `from`: Desde este dia (AAAA-MM-DD).
     * - `to`: Até este dia (AAAA-MM-DD).
     * - `kind`: Só os movimentos deste tipo: `sale`, `fund`, `transfer`, `withdraw`, `return`, `refund`, `med` ou `adjust`.
     *
     * @param string $id O código.
     * @param array{
     *     limit?: float|int,
     *     cursor?: string,
     *     from?: string,
     *     to?: string,
     *     kind?: string
     * } $params
     * @param array{timeout?: float|int, maxRetries?: int} $options
     * @return \WyzeCash\Page<array{
     *     id: string,
     *     kind: string,
     *     kindTitle: string,
     *     direction: string,
     *     amount: int,
     *     gross: int|null,
     *     fee: int,
     *     description: string,
     *     createdAt: string
     * }>
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function listEntries(string $id, array $params = [], array $options = []): \WyzeCash\Page
    {
        return $this->client->list('movimentos_da_subconta', [$id], $params, $options);
    }

    /**
     * Enviar saldo para a subconta
     *
     * Passa um valor do seu saldo livre para a subconta (sem tarifa).
     *
     * **Permissão da chave:** `subaccounts:fund`.
     *
     * Pede o cabeçalho `Idempotency-Key`.
     *
     * **Tira dinheiro da conta de verdade.**
     *
     * `POST /v1/subaccounts/{id}/fund`
     *
     * Campos:
     * - `amount`: Quanto mandar para a subconta, em centavos (sai do saldo livre da conta).
     * - `description`: Descrição.
     *
     * @param string $id O código.
     * @param array{
     *     amount: int,
     *     description?: string
     * } $params
     * @param array{idempotencyKey?: string, timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     id: string,
     *     kind: string,
     *     kindTitle: string,
     *     direction: string,
     *     amount: int,
     *     gross: int|null,
     *     fee: int,
     *     description: string,
     *     createdAt: string,
     *     subaccount: array{
     *         id: string,
     *         externalId: string|null,
     *         balance: int
     *     }
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function fund(string $id, array $params, array $options = []): array
    {
        return $this->client->request('enviar_para_subconta', [$id], $params, $options);
    }

    /**
     * Transferir entre subcontas
     *
     * Passa um valor de uma subconta para outra (`to`), sem tarifa.
     *
     * **Permissão da chave:** `subaccounts:transfer`.
     *
     * Pede o cabeçalho `Idempotency-Key`.
     *
     * **Tira dinheiro da conta de verdade.**
     *
     * `POST /v1/subaccounts/{id}/transfer`
     *
     * Campos:
     * - `to`: A subconta que recebe (o `sacc_` ou o `externalId`).
     * - `amount`: Quanto passar, em centavos (sai do livre desta subconta).
     * - `description`: Descrição.
     *
     * @param string $id O código.
     * @param array{
     *     to: string,
     *     amount: int,
     *     description?: string
     * } $params
     * @param array{idempotencyKey?: string, timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     id: string,
     *     kind: string,
     *     kindTitle: string,
     *     direction: string,
     *     amount: int,
     *     gross: int|null,
     *     fee: int,
     *     description: string,
     *     createdAt: string,
     *     subaccount: array{
     *         id: string,
     *         externalId: string,
     *         balance: int
     *     },
     *     to: array{
     *         id: string,
     *         externalId: string,
     *         balance: int
     *     }
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function transfer(string $id, array $params, array $options = []): array
    {
        return $this->client->request('transferir_entre_subcontas', [$id], $params, $options);
    }

    /**
     * Devolver da subconta para a conta
     *
     * Devolve um valor da subconta para a conta principal.
     *
     * **Permissão da chave:** `subaccounts:return`.
     *
     * Pede o cabeçalho `Idempotency-Key`.
     *
     * **Tira dinheiro da conta de verdade.**
     *
     * `POST /v1/subaccounts/{id}/return`
     *
     * Campos:
     * - `amount`: Quanto devolver à conta principal, em centavos.
     * - `description`: Descrição.
     *
     * @param string $id O código.
     * @param array{
     *     amount: int,
     *     description?: string
     * } $params
     * @param array{idempotencyKey?: string, timeout?: float|int, maxRetries?: int} $options
     * @return array{
     *     id: string,
     *     kind: string,
     *     kindTitle: string,
     *     direction: string,
     *     amount: int,
     *     gross: int|null,
     *     fee: int,
     *     description: string,
     *     createdAt: string,
     *     subaccount: array{
     *         id: string,
     *         externalId: string|null,
     *         balance: int
     *     }
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function returnToAccount(string $id, array $params, array $options = []): array
    {
        return $this->client->request('devolver_para_a_conta', [$id], $params, $options);
    }

    /**
     * Saque da subconta
     *
     * Saque do saldo livre da subconta para a chave Pix do cliente (`destination` ou a chave guardada na subconta) ou para o Pix Copia e Cola dele (`copyPaste`), dentro dos limites dela (`422 SUBACCOUNT_LIMIT`).
     *
     * **Permissão da chave:** `subaccounts:withdraw`.
     *
     * Pede o cabeçalho `Idempotency-Key`.
     *
     * **Tira dinheiro da conta de verdade.**
     *
     * - A chave com `subaccounts:withdraw` só é criada com o 2FA ligado na conta e o código do app autenticador; `withdrawals:create` não vale aqui.
     * - `description` é a sua referência (ex.: o número do pedido): volta na API, no webhook `withdrawal.*` e no comprovante do painel. A resposta traz `subaccount` (`{ id, externalId }`) e, no concluído, `destination.document` (o CPF/CNPJ que o banco confirmou, mascarado) e `destination.holderMatch` (`match`, `different` ou `unknown`).
     * - Pix Copia e Cola (`copyPaste`, estático ou de cobrança com valor): sem valor no código, `amount` é quanto sai da subconta, como com a chave. Com valor no código, `amount` pode faltar: o cliente recebe o valor do código e sai da subconta esse valor mais a taxa (se vier, `amount` tem que ser o valor do código, senão `422 AMOUNT_MISMATCH`).
     * - No modo `cpf_only` da Configuração, a chave é o CPF/CNPJ do titular: outra chave (ou um Copia e Cola que não seja da chave dele) é recusada (`422 SUBACCOUNT_HOLDER_KEY`) e, sem chave no pedido, vai para ela.
     *
     * `POST /v1/subaccounts/{id}/withdrawals`
     *
     * Campos:
     * - `amount`: Quanto sai da subconta, em centavos. Com `copyPaste` de valor, pode faltar: o cliente recebe o valor do código e sai da subconta esse valor mais a taxa (se vier, tem que ser o valor do código).
     * - `destination`: A chave Pix do cliente. Sem ela (e sem `copyPaste`), vale a chave guardada na subconta.
     * - `copyPaste`: Pix Copia e Cola do cliente (estático ou de cobrança com valor), no lugar de `destination`.
     * - `description`: A sua referência (ex.: o número do pedido). Volta no saque e no webhook.
     *
     * @param string $id O código.
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
     *     },
     *     createdAt: string,
     *     completedAt: string|null,
     *     receiptUrl: string|null
     * }
     * @throws \WyzeCash\Exception\WyzeCashException
     */
    public function withdraw(string $id, array $params = [], array $options = []): array
    {
        return $this->client->request('saque_da_subconta', [$id], $params, $options);
    }
}
