<?php

// Gerado por scripts/generate.php a partir de ../openapi.json. Não edite à mão.

declare(strict_types=1);

namespace WyzeCash\Generated;

/** Cada rota da API: método HTTP, caminho e como o SDK trata o pedido. */
final class Operations
{
    /** @var array<string, array{method: string, path: string, query: list<string>, body: bool, idempotent: bool, moneyOut: bool, binary: bool}> */
    public const ALL = [
        'criar_cobranca' => ['method' => 'POST', 'path' => '/v1/payments', 'query' => [], 'body' => true, 'idempotent' => true, 'moneyOut' => false, 'binary' => false],
        'listar_cobrancas' => ['method' => 'GET', 'path' => '/v1/payments', 'query' => ['limit', 'cursor', 'subaccountId', 'customerId', 'status', 'method', 'q'], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'formas_de_pagamento' => ['method' => 'GET', 'path' => '/v1/payments/methods', 'query' => [], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'consultar_cobranca' => ['method' => 'GET', 'path' => '/v1/payments/{id}', 'query' => [], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'criar_link' => ['method' => 'POST', 'path' => '/v1/payment-links', 'query' => [], 'body' => true, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'listar_links' => ['method' => 'GET', 'path' => '/v1/payment-links', 'query' => [], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'consultar_link' => ['method' => 'GET', 'path' => '/v1/payment-links/{id}', 'query' => [], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'editar_link' => ['method' => 'PATCH', 'path' => '/v1/payment-links/{id}', 'query' => [], 'body' => true, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'excluir_link' => ['method' => 'DELETE', 'path' => '/v1/payment-links/{id}', 'query' => [], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'cobrancas_do_link' => ['method' => 'GET', 'path' => '/v1/payment-links/{id}/payments', 'query' => [], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'listar_clientes' => ['method' => 'GET', 'path' => '/v1/customers', 'query' => [], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'criar_cliente' => ['method' => 'POST', 'path' => '/v1/customers', 'query' => [], 'body' => true, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'consultar_cliente' => ['method' => 'GET', 'path' => '/v1/customers/{id}', 'query' => [], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'editar_cliente' => ['method' => 'PATCH', 'path' => '/v1/customers/{id}', 'query' => [], 'body' => true, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'excluir_cliente' => ['method' => 'DELETE', 'path' => '/v1/customers/{id}', 'query' => [], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'saldo' => ['method' => 'GET', 'path' => '/v1/balance', 'query' => [], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'tarifas' => ['method' => 'GET', 'path' => '/v1/fees', 'query' => [], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'previa_tarifa' => ['method' => 'GET', 'path' => '/v1/fees/preview', 'query' => ['operation', 'amount'], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'extrato' => ['method' => 'GET', 'path' => '/v1/statement', 'query' => ['cursor', 'limit', 'days', 'from', 'to', 'direction', 'category', 'method', 'q'], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'listar_transacoes' => ['method' => 'GET', 'path' => '/v1/transactions', 'query' => ['limit', 'cursor', 'type', 'status', 'q'], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'consultar_transacao' => ['method' => 'GET', 'path' => '/v1/transactions/{id}', 'query' => [], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'buscar' => ['method' => 'GET', 'path' => '/v1/search', 'query' => ['q'], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'comprovante' => ['method' => 'GET', 'path' => '/v1/receipts/{id}', 'query' => [], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'listar_meds' => ['method' => 'GET', 'path' => '/v1/meds', 'query' => ['limit', 'cursor', 'status'], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'consultar_med' => ['method' => 'GET', 'path' => '/v1/meds/{id}', 'query' => [], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'baixar_prova_med' => ['method' => 'GET', 'path' => '/v1/meds/{id}/attachments/{attachmentId}', 'query' => [], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => true],
        'enviar_pix' => ['method' => 'POST', 'path' => '/v1/pix/payments', 'query' => [], 'body' => true, 'idempotent' => true, 'moneyOut' => true, 'binary' => false],
        'listar_pix_enviados' => ['method' => 'GET', 'path' => '/v1/pix/payments', 'query' => ['limit', 'cursor'], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'consultar_pix_enviado' => ['method' => 'GET', 'path' => '/v1/pix/payments/{id}', 'query' => [], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'criar_saque' => ['method' => 'POST', 'path' => '/v1/withdrawals', 'query' => [], 'body' => true, 'idempotent' => true, 'moneyOut' => true, 'binary' => false],
        'listar_saques' => ['method' => 'GET', 'path' => '/v1/withdrawals', 'query' => ['limit', 'cursor', 'subaccountId'], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'consultar_saque' => ['method' => 'GET', 'path' => '/v1/withdrawals/{id}', 'query' => [], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'criar_estorno' => ['method' => 'POST', 'path' => '/v1/refunds', 'query' => [], 'body' => true, 'idempotent' => true, 'moneyOut' => true, 'binary' => false],
        'listar_estornos' => ['method' => 'GET', 'path' => '/v1/refunds', 'query' => ['limit', 'cursor'], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'consultar_estorno' => ['method' => 'GET', 'path' => '/v1/refunds/{id}', 'query' => [], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'criar_subconta' => ['method' => 'POST', 'path' => '/v1/subaccounts', 'query' => [], 'body' => true, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'listar_subcontas' => ['method' => 'GET', 'path' => '/v1/subaccounts', 'query' => ['limit', 'cursor'], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'consultar_subconta' => ['method' => 'GET', 'path' => '/v1/subaccounts/{id}', 'query' => [], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'editar_subconta' => ['method' => 'PATCH', 'path' => '/v1/subaccounts/{id}', 'query' => [], 'body' => true, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'excluir_subconta' => ['method' => 'DELETE', 'path' => '/v1/subaccounts/{id}', 'query' => [], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'configuracao_subcontas' => ['method' => 'GET', 'path' => '/v1/subaccounts/settings', 'query' => [], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'mudar_configuracao_subcontas' => ['method' => 'PUT', 'path' => '/v1/subaccounts/settings', 'query' => [], 'body' => true, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'exemplo_configuracao_subcontas' => ['method' => 'GET', 'path' => '/v1/subaccounts/settings/preview', 'query' => ['saleFeeBps', 'saleFeeFixed', 'withdrawFeeBps', 'withdrawFeeFixed'], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'movimentos_da_subconta' => ['method' => 'GET', 'path' => '/v1/subaccounts/{id}/entries', 'query' => ['limit', 'cursor', 'from', 'to', 'kind'], 'body' => false, 'idempotent' => false, 'moneyOut' => false, 'binary' => false],
        'enviar_para_subconta' => ['method' => 'POST', 'path' => '/v1/subaccounts/{id}/fund', 'query' => [], 'body' => true, 'idempotent' => true, 'moneyOut' => true, 'binary' => false],
        'transferir_entre_subcontas' => ['method' => 'POST', 'path' => '/v1/subaccounts/{id}/transfer', 'query' => [], 'body' => true, 'idempotent' => true, 'moneyOut' => true, 'binary' => false],
        'devolver_para_a_conta' => ['method' => 'POST', 'path' => '/v1/subaccounts/{id}/return', 'query' => [], 'body' => true, 'idempotent' => true, 'moneyOut' => true, 'binary' => false],
        'saque_da_subconta' => ['method' => 'POST', 'path' => '/v1/subaccounts/{id}/withdrawals', 'query' => [], 'body' => true, 'idempotent' => true, 'moneyOut' => true, 'binary' => false],
    ];
}
