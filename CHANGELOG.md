# Mudanças

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
