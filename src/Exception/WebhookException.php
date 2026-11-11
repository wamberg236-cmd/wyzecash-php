<?php

declare(strict_types=1);

namespace WyzeCash\Exception;

/** O aviso (webhook) não passou na conferência da assinatura. */
final class WebhookException extends WyzeCashException
{
    /** @param 'MISSING_SIGNATURE'|'INVALID_SIGNATURE'|'TIMESTAMP_OUT_OF_RANGE'|'INVALID_PAYLOAD' $errorCode */
    public function __construct(string $message, string $errorCode)
    {
        parent::__construct($message, 400, $errorCode);
    }
}
