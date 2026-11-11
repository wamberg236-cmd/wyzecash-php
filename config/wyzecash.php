<?php

// Configuração da WyzeCash no Laravel (php artisan vendor:publish --tag=wyzecash-config).

return [
    // A chave de API (bk_live_...), criada no painel em Integrações e API.
    'api_key' => env('WYZECASH_API_KEY'),

    // O segredo do webhook (painel › Integrações e API › Webhooks), para o middleware wyzecash.webhook.
    'webhook_secret' => env('WYZECASH_WEBHOOK_SECRET'),

    // Quantos segundos o aviso vale depois de assinado.
    'webhook_tolerance' => 300,

    // Tempo máximo de cada pedido à API, em segundos, e quantas vezes tentar de novo.
    'timeout' => 60,
    'max_retries' => 2,
];
