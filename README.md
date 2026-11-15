# wyzecash/wyzecash

SDK oficial da [WyzeCash](https://docs.wyzecash.com.br) para PHP 8.1+: cobranças por Pix, cartão e cripto, links de pagamento, saldo e extrato, saques, estornos, MED, subcontas e webhooks. Precisa só das extensões `curl` e `json`, e já vem com a integração para o Laravel.

```bash
composer require wyzecash/wyzecash
```

## Começar

```php
use WyzeCash\WyzeCash;

$wyzecash = new WyzeCash(getenv('WYZECASH_API_KEY'));

$cobranca = $wyzecash->payments->create([
    'amount' => 15000, // R$ 150,00, sempre em centavos
    'description' => 'Pedido #1052',
]);

echo $cobranca['copyPaste']; // Pix Copia e Cola
```

A chave de API (`wyze_…`) é criada no painel, em Integrações e API, com uma permissão por rota. Sem passar a chave, o SDK lê a variável `WYZECASH_API_KEY`.

Não existe ambiente de testes: todo pedido é de verdade. Para testar, faça uma cobrança pequena.

Os campos têm os mesmos nomes da API (`subaccountId`, `feePaidBy`...) e as respostas voltam como arrays. O formato de cada pedido e resposta está nos comentários de cada método, que o PhpStorm e o PHPStan entendem (completam os campos e avisam quando falta algum).

## Recursos

| Recurso | Métodos |
|---|---|
| `payments` | `create`, `methods`, `list`, `retrieve` |
| `paymentLinks` | `create`, `list`, `retrieve`, `update`, `delete`, `listPayments` |
| `customers` | `create`, `list`, `retrieve`, `update`, `delete` |
| `balance` | `retrieve` |
| `fees` | `list`, `preview` |
| `statement` | `list` |
| `transactions` | `list`, `retrieve` |
| `search` | `query` |
| `receipts` | `retrieve` |
| `meds` | `list`, `retrieve`, `downloadAttachment` |
| `withdrawals` | `create`, `list`, `retrieve` |
| `refunds` | `create`, `list`, `retrieve` |
| `subaccounts` | `create`, `list`, `retrieve`, `update`, `delete`, `retrieveSettings`, `updateSettings`, `previewSettings`, `listEntries`, `fund`, `transfer`, `returnToAccount`, `withdraw` |

Os códigos vão antes dos campos: `$wyzecash->subaccounts->fund('sacc_…', ['amount' => 1000])`. Campo com `null` vai como `null`, que em alguns campos limpa o valor (ex.: `'limits' => ['perWithdrawal' => null]` tira o limite próprio da subconta).

## Listas

A lista traz a primeira página; o `foreach` passa por todas, buscando a próxima só quando precisa.

```php
$pagina = $wyzecash->payments->list(['status' => 'paid', 'limit' => 50]);
echo count($pagina->items), ' ', $pagina->nextCursor;

foreach ($wyzecash->payments->list(['status' => 'paid']) as $cobranca) {
    echo $cobranca['id'], ' ', $cobranca['amount'], PHP_EOL;
}
```

`$pagina->nextPage()` busca a próxima página e `$pagina->toArray(500)` junta os itens numa lista só.

## Idempotency-Key

Criar cobrança, saque, Pix enviado, estorno e os envios das subcontas pedem a `Idempotency-Key`. Passe o código do pedido no seu sistema: repetir a mesma chave devolve o mesmo resultado, sem fazer de novo.

```php
$wyzecash->withdrawals->create(
    ['amount' => 10000, 'destination' => ['keyType' => 'email', 'key' => 'financeiro@exemplo.com']],
    ['idempotencyKey' => 'saque-1052'],
);
```

Sem ela, o SDK cria uma chave por chamada.

## Erros e novas tentativas

```php
use WyzeCash\Exception\WyzeCashException;

try {
    $wyzecash->payments->create(['amount' => 15000]);
} catch (WyzeCashException $erro) {
    echo $erro->status, ' ', $erro->errorCode, ' ', $erro->getMessage(), ' ', $erro->requestId;
}
```

- O `errorCode` é estável (`VALIDATION`, `INSUFFICIENT_FUNDS`, `RATE_LIMITED`...) e a mensagem está em português.
- `429` (passou de 100 pedidos por minuto) volta a tentar sozinho, esperando o `Retry-After`.
- Erro de rede e `5xx` voltam a tentar só nas consultas e na criação de cobrança.
- O que tira dinheiro (saque, Pix enviado, estorno, envios das subcontas) **nunca** é repetido sozinho: se a rede cair, confira pela consulta ou pelo webhook antes de pedir de novo.
- Rede fora ou tempo esgotado lançam `WyzeCash\Exception\ConnectionException` (`errorCode` `CONNECTION_ERROR` ou `TIMEOUT`).

Opções do cliente: `new WyzeCash($chave, ['maxRetries' => 2, 'timeout' => 60, 'baseUrl' => ..., 'transport' => ...])`. Cada método também aceita `['timeout' => 10, 'maxRetries' => 0]` no último parâmetro.

## Saber quando foi pago

**Use os webhooks (ou o WebSocket), não `$wyzecash->payments->retrieve()` em sequência.**

Chamar a consulta da cobrança em sequência para ver se ela foi paga gasta o limite de **100 pedidos por minuto da conta** (somando todas as rotas e todas as chaves). Uma tela consultando a cada 2 segundos gasta 30 por minuto; com quatro abertas, **todo** pedido da conta, até criar cobrança, volta `429 RATE_LIMITED` até liberar. Os avisos do webhook e do WebSocket não contam no limite.

- O seu sistema tem um endereço HTTPS: use os [webhooks](#webhooks) (`payment.paid`, `payment.expired`, `withdrawal.completed`...).
- Não tem (bot de Discord ou Telegram, programa num VPS): o seu servidor conecta no WebSocket `wss://api.wyzecash.com.br/v1/ws` com a chave e recebe os mesmos eventos ([como funciona](https://docs.wyzecash.com.br/webhooks/websocket)).
- A tela de "aguardando pagamento" pergunta ao **seu** servidor (que o webhook atualizou), nunca direto à WyzeCash: a chave de API não vai para o navegador nem para o app.
- `$wyzecash->payments->retrieve()` só para conferir uma vez (o webhook não chegou em alguns minutos). Sem webhook nem WebSocket, consulte no máximo a cada 5 segundos e pare na primeira situação final (`paid`, `expired` ou `refunded`).

## Webhooks

Cada aviso chega com o cabeçalho `X-WyzeCash-Signature` (junto vêm o `X-WyzeCash-Event`, com o tipo do evento, e o `X-WyzeCash-Delivery`, com o código da entrega). Até 30/11/2026 os antigos `X-Bukio-*` vêm junto, com o mesmo valor; a partir de 01/12/2026, só os novos. Confira sempre com o corpo cru, como chegou:

```php
use WyzeCash\Exception\WebhookException;
use WyzeCash\Webhook;

try {
    $evento = Webhook::verify(file_get_contents('php://input'), $_SERVER['HTTP_X_WYZECASH_SIGNATURE'] ?? null, getenv('WYZECASH_WEBHOOK_SECRET'));
} catch (WebhookException $erro) {
    http_response_code(400);
    exit;
}

if ($evento['type'] === 'payment.paid') {
    // marque o pedido como pago (use $evento['id'] para não processar duas vezes)
}
http_response_code(200);
```

Aviso com assinatura errada, sem assinatura ou assinado há mais de 5 minutos lança `WebhookException`. Os nomes dos cabeçalhos ficam em `Webhook::SIGNATURE_HEADER` (`X-WyzeCash-Signature`) e `Webhook::LEGACY_SIGNATURE_HEADER` (o antigo, `X-Bukio-Signature`). Responda `200` quando deu certo; com qualquer outra resposta, a WyzeCash manda de novo mais tarde.

## Laravel

O provider entra sozinho (package discovery). No `.env`:

```bash
WYZECASH_API_KEY=wyze_...
WYZECASH_WEBHOOK_SECRET=...
```

Receba o cliente por injeção:

```php
use WyzeCash\WyzeCash;

final class CobrancaController
{
    public function store(WyzeCash $wyzecash)
    {
        return $wyzecash->payments->create(['amount' => 15000, 'description' => 'Pedido #1052']);
    }
}
```

E o webhook com o middleware `wyzecash.webhook`, que confere a assinatura do `X-WyzeCash-Signature` (sem ele, a do antigo `X-Bukio-Signature`; aviso que não é da WyzeCash recebe `400` e não chega à sua rota). Ponha a rota em `routes/api.php`, que não tem CSRF:

```php
Route::post('/webhooks/wyzecash', function (Illuminate\Http\Request $request) {
    $evento = $request->attributes->get('wyzecash_event');
    if ($evento['type'] === 'payment.paid') {
        // marque o pedido como pago
    }
    return response()->json(['received' => true]);
})->middleware('wyzecash.webhook');
```

Para mudar o tempo dos pedidos ou as novas tentativas: `php artisan vendor:publish --tag=wyzecash-config`.

## Atualizar da 0.2.0

```bash
composer require wyzecash/wyzecash:^0.3
```

> **Atualize antes de 01/12/2026.** A 0.2.0 só lê o cabeçalho `X-Bukio-Signature`, que deixa de vir nos webhooks em 01/12/2026: a partir desse dia, o middleware `wyzecash.webhook` da 0.2.0 recusa todos os avisos.

| Na 0.2.0 | Na 0.3.0 |
| --- | --- |
| Cabeçalho `X-Bukio-Signature` | `X-WyzeCash-Signature`. Até 30/11/2026 o antigo vem junto, com o mesmo valor |
| `Webhook::SIGNATURE_HEADER` é `X-Bukio-Signature` | `Webhook::SIGNATURE_HEADER` é `X-WyzeCash-Signature`, e o antigo fica em `Webhook::LEGACY_SIGNATURE_HEADER` |
| Chave de API `bk_live_…` | `wyze_…` (a mesma chave, só o começo muda) |

**Webhooks:** com o middleware `wyzecash.webhook` do Laravel, nada muda no seu código: ele lê o cabeçalho novo e, sem ele, o antigo. Se você lê o cabeçalho na mão, passe o novo para o `Webhook::verify` (a assinatura continua no mesmo formato):

```php
// Antes (0.2.0)
$evento = Webhook::verify(file_get_contents('php://input'), $_SERVER['HTTP_X_BUKIO_SIGNATURE'] ?? null, getenv('WYZECASH_WEBHOOK_SECRET'));

// Agora (0.3.0)
$evento = Webhook::verify(file_get_contents('php://input'), $_SERVER['HTTP_X_WYZECASH_SIGNATURE'] ?? null, getenv('WYZECASH_WEBHOOK_SECRET'));
```

**Chave de API:** troque o começo da chave onde ela fica guardada (a variável `WYZECASH_API_KEY` do `.env`): `bk_live_ABC123_XYZ` vira `wyze_ABC123_XYZ`. A `bk_live_` vale até 30/11/2026; a partir de 01/12/2026, a API recusa com `401 OLD_KEY_PREFIX`. O resto do código não muda.

**O que mais mudou:** `$wyzecash->subaccounts->updateSettings()` aceita `medFeeToSubaccount`, para repassar a taxa de MED à subconta.

## Documentação

Referência completa da API: https://docs.wyzecash.com.br
