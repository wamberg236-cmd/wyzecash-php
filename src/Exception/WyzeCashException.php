<?php

declare(strict_types=1);

namespace WyzeCash\Exception;

/**
 * Erro da API (ou do pedido que nem chegou nela, com `status` 0). A API responde
 * `{ "error": { "code", "message", "details" }, "requestId" }`: o `errorCode` é estável (use no seu
 * sistema) e a mensagem está em português.
 */
class WyzeCashException extends \RuntimeException
{
    /**
     * @param int $status Situação HTTP (0 quando o pedido nem chegou à API).
     * @param string $errorCode Código estável do erro (ex.: `VALIDATION`, `INSUFFICIENT_FUNDS`, `RATE_LIMITED`).
     * @param string|null $requestId Código do pedido: mande para o suporte quando precisar de ajuda.
     * @param array<string, mixed>|null $details
     */
    public function __construct(
        string $message,
        public readonly int $status,
        public readonly string $errorCode,
        public readonly ?string $requestId = null,
        public readonly ?array $details = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $status, $previous);
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getRequestId(): ?string
    {
        return $this->requestId;
    }

    /** @return array<string, mixed>|null */
    public function getDetails(): ?array
    {
        return $this->details;
    }
}
