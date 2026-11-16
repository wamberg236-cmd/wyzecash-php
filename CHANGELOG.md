# Mudanças

## 0.3.1 (16/11/2026)

- Os links do pacote apontam para o site (`https://wyzecash.com.br`) e para a documentação (`https://docs.wyzecash.com.br`). O código não mudou.

## 0.3.0 (15/11/2026)

**Atualize antes de 01/12/2026:** a 0.2.0 só lê o cabeçalho `X-Bukio-Signature`, que deixa de vir nos webhooks em 01/12/2026. A partir desse dia, o middleware `wyzecash.webhook` da 0.2.0 recusa todos os avisos.

**Mudanças que pedem ajuste no seu código:**

- Os webhooks passam a vir com os cabeçalhos `X-WyzeCash-Signature`, `X-WyzeCash-Event` e `X-WyzeCash-Delivery`. Até 30/11/2026 (23h59 de Brasília) os antigos `X-Bukio-*` vêm junto, com o mesmo valor; a partir de 01/12/2026, só os novos. A assinatura continua no mesmo formato.
- `Webhook::SIGNATURE_HEADER` passa a ser `X-WyzeCash-Signature`; o antigo fica em `Webhook::LEGACY_SIGNATURE_HEADER` (`X-Bukio-Signature`). O middleware `wyzecash.webhook` do Laravel lê o cabeçalho novo primeiro e, sem ele, o antigo (com o middleware, nada muda no seu código). Se você lê o cabeçalho na mão, passe o `X-WyzeCash-Signature` (`$_SERVER['HTTP_X_WYZECASH_SIGNATURE']`) para o `Webhook::verify`.
- A chave de API passa a começar com `wyze_` no lugar de `bk_live_` (a mesma chave, só o começo muda: `bk_live_ABC123_XYZ` vira `wyze_ABC123_XYZ`). A `bk_live_` vale até 30/11/2026; a partir de 01/12/2026, a API recusa com `401 OLD_KEY_PREFIX`. Troque o começo da chave na variável `WYZECASH_API_KEY`.
- As mensagens da `WebhookException` citam o `X-WyzeCash-Signature` (os códigos, como `MISSING_SIGNATURE`, não mudam).

**Novidades:**

- `subaccounts->updateSettings` aceita `medFeeToSubaccount`: repassar a taxa de MED à subconta (a Taxa de MED recebido de um MED numa venda da subconta sai dela).

## 0.2.0

**Mudanças que pedem ajuste no seu código:**

- Saem `$wyzecash->pix->send()`, `->list()` e `->retrieve()`: enviar Pix e sacar são o mesmo Pix que sai, então use `$wyzecash->withdrawals->create()` (com `destination` ou `copyPaste`). A rota antiga `/v1/pix/payments` continua respondendo por enquanto, mas vai ser removida.
- Sai `$wyzecash->meds->downloadAttachment()`: as provas do MED são internas da WyzeCash e não se baixam pela API (a rota já recusava).

**Novidades:**

- `$wyzecash->meds->respond($id, ['defense' => ..., 'evidence' => [...]])`: responde um MED com a defesa e até 5 provas (em base64), num pedido só. Precisa da permissão `/meds/respond` na chave.
- O endereço padrão passa a ser `https://api.wyzecash.com.br`. O antigo continua funcionando, e quem passa `baseUrl` não muda nada.
- `payments->retrieve()` e `withdrawals->retrieve()` explicam no editor que, para saber quando a cobrança é paga ou o saque conclui, o caminho é o webhook (ou o WebSocket), não a consulta em sequência, que gasta o limite de 100 pedidos por minuto da conta. O README ganhou a seção "Saber quando foi pago".

## 0.1.0

Primeira versão.
