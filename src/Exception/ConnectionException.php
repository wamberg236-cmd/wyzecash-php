<?php

declare(strict_types=1);

namespace WyzeCash\Exception;

/** O pedido não chegou à API ou a resposta não voltou (rede, tempo esgotado). */
final class ConnectionException extends WyzeCashException
{
    /** @param 'CONNECTION_ERROR'|'TIMEOUT' $errorCode */
    public function __construct(string $message, string $errorCode = 'CONNECTION_ERROR', ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $errorCode, null, null, $previous);
    }
}
