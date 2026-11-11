<?php

// Gerado por scripts/generate.php a partir de ../openapi.json. Não edite à mão.

declare(strict_types=1);

namespace WyzeCash\Generated;

use WyzeCash\Requester;

/** Os recursos da API, um por grupo de rotas. */
trait Resources
{
    public readonly PaymentsResource $payments;
    public readonly PaymentLinksResource $paymentLinks;
    public readonly CustomersResource $customers;
    public readonly BalanceResource $balance;
    public readonly FeesResource $fees;
    public readonly StatementResource $statement;
    public readonly TransactionsResource $transactions;
    public readonly SearchResource $search;
    public readonly ReceiptsResource $receipts;
    public readonly MedsResource $meds;
    public readonly PixResource $pix;
    public readonly WithdrawalsResource $withdrawals;
    public readonly RefundsResource $refunds;
    public readonly SubaccountsResource $subaccounts;

    private function initResources(Requester $client): void
    {
        $this->payments = new PaymentsResource($client);
        $this->paymentLinks = new PaymentLinksResource($client);
        $this->customers = new CustomersResource($client);
        $this->balance = new BalanceResource($client);
        $this->fees = new FeesResource($client);
        $this->statement = new StatementResource($client);
        $this->transactions = new TransactionsResource($client);
        $this->search = new SearchResource($client);
        $this->receipts = new ReceiptsResource($client);
        $this->meds = new MedsResource($client);
        $this->pix = new PixResource($client);
        $this->withdrawals = new WithdrawalsResource($client);
        $this->refunds = new RefundsResource($client);
        $this->subaccounts = new SubaccountsResource($client);
    }
}
