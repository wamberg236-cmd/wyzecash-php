<?php

declare(strict_types=1);

namespace WyzeCash\Http;

use WyzeCash\Exception\ConnectionException;

/**
 * O transporte padrão: cURL, com a mesma conexão reaproveitada entre os pedidos (keep-alive), o
 * certificado conferido e só HTTP e HTTPS.
 */
final class CurlTransport implements Transport
{
    private ?\CurlHandle $handle = null;

    public function send(string $method, string $url, array $headers, ?string $body, float $timeout): Response
    {
        $this->handle ??= curl_init() ?: throw new ConnectionException('Não deu para iniciar o cURL.');
        $handle = $this->handle;
        curl_reset($handle);

        $lines = [];
        foreach ($headers as $name => $value) {
            $lines[] = $name . ': ' . $value;
        }
        $received = [];
        $milliseconds = max(1, (int) round($timeout * 1000));
        curl_setopt_array($handle, [
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $lines,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_TIMEOUT_MS => $milliseconds,
            CURLOPT_CONNECTTIMEOUT_MS => min($milliseconds, 10_000),
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            CURLOPT_HEADERFUNCTION => static function ($curl, string $line) use (&$received): int {
                $parts = explode(':', $line, 2);
                if (count($parts) === 2) {
                    $received[strtolower(trim($parts[0]))] = trim($parts[1]);
                }
                return strlen($line);
            },
        ]);
        if ($body !== null) {
            curl_setopt($handle, CURLOPT_POSTFIELDS, $body);
        }

        $content = curl_exec($handle);
        if ($content === false || !is_string($content)) {
            $errno = curl_errno($handle);
            $error = curl_error($handle);
            if ($errno === CURLE_OPERATION_TIMEDOUT) {
                throw new ConnectionException('A API não respondeu a tempo.', 'TIMEOUT');
            }
            throw new ConnectionException('Não deu para falar com a API: ' . ($error !== '' ? $error : 'erro ' . $errno));
        }
        /** @var array<string, string> $received */
        return new Response((int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE), $received, $content);
    }
}
