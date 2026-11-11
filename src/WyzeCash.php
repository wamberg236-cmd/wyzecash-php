<?php

declare(strict_types=1);

namespace WyzeCash;

use WyzeCash\Exception\ConnectionException;
use WyzeCash\Exception\WyzeCashException;
use WyzeCash\Generated\Operations;
use WyzeCash\Generated\Resources;
use WyzeCash\Http\CurlTransport;
use WyzeCash\Http\Response;
use WyzeCash\Http\Transport;

/**
 * O cliente da API da WyzeCash: `new WyzeCash($chave)` e os recursos
 * (`$wyzecash->payments->create([...])`). Precisa só das extensões curl e json.
 *
 * Novas tentativas: 429 (passou do limite) sempre volta a tentar, esperando o `Retry-After`, porque
 * a API não fez nada. Erro de rede, tempo esgotado e 5xx só voltam a tentar nas consultas e na
 * criação de cobrança (com a mesma Idempotency-Key); o que tira dinheiro (saque, Pix enviado,
 * estorno, envios das subcontas) nunca é repetido sozinho: confira pela consulta ou pelo webhook.
 */
final class WyzeCash implements Requester
{
    use Resources;

    public const VERSION = '0.1.0';

    public const DEFAULT_BASE_URL = 'https://bukio-backend-production.up.railway.app';

    private readonly string $apiKey;

    private readonly string $baseUrl;

    private readonly int $maxRetries;

    private readonly float $timeout;

    private readonly Transport $transport;

    /** @var \Closure(float): void */
    private readonly \Closure $sleep;

    /**
     * @param string|null $apiKey A chave de API (`bk_live_…`). Sem ela, vale a variável de ambiente `WYZECASH_API_KEY`.
     * @param array{baseUrl?: string, maxRetries?: int, timeout?: float|int, transport?: Transport, sleep?: \Closure(float): void} $options
     *     `baseUrl` (padrão: o de produção), `maxRetries` (padrão 2), `timeout` em segundos (padrão 60),
     *     `transport` (outro jeito de fazer o pedido HTTP) e `sleep` (para testes).
     */
    public function __construct(?string $apiKey = null, array $options = [])
    {
        $key = $apiKey ?? self::env('WYZECASH_API_KEY');
        if ($key === null || $key === '') {
            throw new \InvalidArgumentException('Passe a chave de API: new WyzeCash(getenv("WYZECASH_API_KEY")).');
        }
        $this->apiKey = $key;
        $this->baseUrl = rtrim($options['baseUrl'] ?? self::DEFAULT_BASE_URL, '/');
        $this->maxRetries = $options['maxRetries'] ?? 2;
        $this->timeout = (float) ($options['timeout'] ?? 60);
        $this->transport = $options['transport'] ?? new CurlTransport();
        $this->sleep = $options['sleep'] ?? static function (float $seconds): void {
            usleep((int) round($seconds * 1_000_000));
        };
        $this->initResources($this);
    }

    /** @return array<string, string> */
    public function __debugInfo(): array
    {
        return ['baseUrl' => $this->baseUrl];
    }

    public function request(string $operationId, array $pathArgs, array $params, array $options = []): mixed
    {
        return $this->send($operationId, $pathArgs, $params, $options);
    }

    public function list(string $operationId, array $pathArgs, array $params, array $options = []): Page
    {
        $start = $params['cursor'] ?? null;
        unset($params['cursor']);
        $fetch = function (?string $cursor) use ($operationId, $pathArgs, $params, $options): array {
            $result = $this->send($operationId, $pathArgs, $cursor !== null ? $params + ['cursor' => $cursor] : $params, $options);
            /** @var array<string, mixed> */
            return is_array($result) ? $result : [];
        };
        return new Page($fetch, $fetch(is_string($start) && $start !== '' ? $start : null));
    }

    /**
     * @param list<string> $pathArgs
     * @param array<string, mixed> $params
     * @param array{idempotencyKey?: string, timeout?: float|int, maxRetries?: int} $options
     */
    private function send(string $operationId, array $pathArgs, array $params, array $options): mixed
    {
        $op = Operations::ALL[$operationId] ?? throw new \InvalidArgumentException('Rota desconhecida: ' . $operationId);
        $path = $op['path'];
        foreach ($pathArgs as $arg) {
            if ($arg === '') {
                throw new \InvalidArgumentException('Faltou o código na rota ' . $op['path'] . '.');
            }
            // rawurlencode só deixa letras, números, "-_.~" e "%": seguro como texto de troca.
            $path = (string) preg_replace('/\{[A-Za-z]+\}/', rawurlencode($arg), $path, 1);
        }

        $query = [];
        $body = [];
        foreach ($params as $name => $value) {
            if (in_array($name, $op['query'], true)) {
                if ($value !== null) {
                    $query[$name] = is_bool($value) ? ($value ? 'true' : 'false') : (is_scalar($value) ? (string) $value : '');
                }
            } else {
                $body[$name] = $value;
            }
        }
        $url = $this->baseUrl . $path . ($query ? '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986) : '');

        $headers = [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => $op['binary'] ? '*/*' : 'application/json',
            'User-Agent' => 'wyzecash-php/' . self::VERSION,
        ];
        if ($op['idempotent']) {
            $headers['Idempotency-Key'] = $options['idempotencyKey'] ?? self::uuid();
        }
        $payload = null;
        if ($op['body']) {
            $payload = $body === [] ? '{}' : json_encode($body, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
            $headers['Content-Type'] = 'application/json';
        }

        $safeToRepeat = in_array($op['method'], ['GET', 'DELETE'], true) || ($op['idempotent'] && !$op['moneyOut']);
        $retries = $options['maxRetries'] ?? $this->maxRetries;
        $timeout = (float) ($options['timeout'] ?? $this->timeout);
        for ($attempt = 0; ; $attempt++) {
            try {
                $response = $this->transport->send($op['method'], $url, $headers, $payload, $timeout);
            } catch (ConnectionException $error) {
                if ($safeToRepeat && $attempt < $retries) {
                    ($this->sleep)(self::backoff($attempt));
                    continue;
                }
                throw $error;
            }

            if ($response->status >= 200 && $response->status < 300) {
                if ($op['binary']) {
                    return $response->body;
                }
                if ($response->body === '') {
                    return null;
                }
                try {
                    return json_decode($response->body, true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $error) {
                    throw new WyzeCashException('A API respondeu algo que não é JSON.', $response->status, 'INVALID_RESPONSE', $response->header('x-request-id'), null, $error);
                }
            }
            $retryable = $response->status === 429 || ($response->status >= 500 && $safeToRepeat);
            if ($retryable && $attempt < $retries) {
                ($this->sleep)(self::retryAfter($response->header('retry-after')) ?? self::backoff($attempt));
                continue;
            }
            throw self::toError($response);
        }
    }

    private static function backoff(int $attempt): float
    {
        return min(8.0, 0.5 * (2 ** $attempt)) * (0.75 + mt_rand() / mt_getrandmax() * 0.5);
    }

    private static function retryAfter(?string $value): ?float
    {
        if ($value === null || !is_numeric($value) || (float) $value <= 0) {
            return null;
        }
        return min((float) $value, 60.0);
    }

    private static function toError(Response $response): WyzeCashException
    {
        $data = json_decode($response->body, true);
        $body = is_array($data) ? $data : [];
        $error = is_array($body['error'] ?? null) ? $body['error'] : [];
        $message = is_string($error['message'] ?? null) && $error['message'] !== '' ? $error['message'] : 'A API respondeu ' . $response->status . '.';
        $code = is_string($error['code'] ?? null) && $error['code'] !== '' ? $error['code'] : 'HTTP_' . $response->status;
        $requestId = is_string($body['requestId'] ?? null) ? $body['requestId'] : $response->header('x-request-id');
        /** @var array<string, mixed>|null $details */
        $details = is_array($error['details'] ?? null) ? $error['details'] : null;
        return new WyzeCashException($message, $response->status, $code, $requestId, $details);
    }

    private static function uuid(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0F) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3F) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }

    private static function env(string $name): ?string
    {
        $value = getenv($name);
        if (is_string($value) && $value !== '') {
            return $value;
        }
        $value = $_ENV[$name] ?? $_SERVER[$name] ?? null;
        return is_string($value) && $value !== '' ? $value : null;
    }
}
