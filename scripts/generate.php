<?php

// Gera src/Generated/ a partir de ../openapi.json (a cópia da especificação da API, a mesma dos
// SDKs de Node e Python): a tabela das rotas e uma classe por recurso, com o formato de cada pedido
// e resposta nos comentários (array shapes, que o PhpStorm e o PHPStan leem). Nunca editar
// src/Generated/ à mão: mudou a API, rode `bun run sdk:spec` na raiz do backend e
// `php scripts/generate.php` aqui. O teste tests/GeneratedTest.php falha se ficar diferente.

declare(strict_types=1);

const HEADER = "<?php\n\n// Gerado por scripts/generate.php a partir de ../openapi.json. Não edite à mão.\n\ndeclare(strict_types=1);\n\nnamespace WyzeCash\\Generated;\n";

/** "payments.listPayments" vira "PaymentsListPayments". */
function pascal(string $text): string
{
    return (string) preg_replace_callback('/(?:^|[._\-])(\w)/', static fn (array $m): string => strtoupper($m[1]), $text);
}

function shapeKey(string $name): string
{
    return preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name) ? $name : var_export($name, true);
}

/** Esquema JSON no tipo dos comentários do PHP (array shapes). */
function phpType(?array $schema, string $indent = ''): string
{
    if (!$schema) {
        return 'mixed';
    }
    $types = isset($schema['type']) ? (array) $schema['type'] : [];
    if (isset($schema['enum'])) {
        $values = array_map(static fn ($v): string => $v === null ? 'null' : (is_string($v) ? var_export($v, true) : json_encode($v)), $schema['enum']);
        if (in_array('null', $types, true) && !in_array('null', $values, true)) {
            $values[] = 'null';
        }
        return implode('|', array_unique($values));
    }
    if (!$types) {
        return isset($schema['properties']) ? objectType($schema, $indent) : 'mixed';
    }
    $parts = [];
    foreach ($types as $type) {
        $parts[] = match ($type) {
            'string' => 'string',
            'integer' => 'int',
            'number' => 'float|int',
            'boolean' => 'bool',
            'null' => 'null',
            'array' => 'list<' . phpType($schema['items'] ?? null, $indent) . '>',
            'object' => objectType($schema, $indent),
            default => 'mixed',
        };
    }
    return implode('|', $parts);
}

function objectType(array $schema, string $indent): string
{
    $properties = $schema['properties'] ?? [];
    $extra = $schema['additionalProperties'] ?? null;
    if (!$properties) {
        return is_array($extra) && $extra ? 'array<string, ' . phpType($extra, $indent) . '>' : 'array<string, mixed>';
    }
    $required = $schema['required'] ?? [];
    $inner = $indent . '    ';
    $fields = [];
    foreach ($properties as $name => $value) {
        $fields[] = $inner . shapeKey((string) $name) . (in_array($name, $required, true) ? '' : '?') . ': ' . phpType($value, $inner);
    }
    return "array{\n" . implode(",\n", $fields) . "\n" . $indent . '}';
}

/** @return list<array<string, mixed>> */
function operations(array $spec): array
{
    $out = [];
    foreach ($spec['paths'] as $path => $methods) {
        foreach ($methods as $http => $op) {
            [$resource, $method] = explode('.', (string) $op['x-sdk-method']);
            $params = $op['parameters'] ?? [];
            $query = array_values(array_filter($params, static fn (array $p): bool => $p['in'] === 'query'));
            $ok = [];
            foreach ($op['responses'] as $code => $response) {
                if (str_starts_with((string) $code, '2')) {
                    $ok = $response;
                    break;
                }
            }
            $content = $ok['content'] ?? [];
            $response = $content['application/json']['schema'] ?? null;
            $props = $response['properties'] ?? [];
            $out[] = [
                'id' => $op['operationId'],
                'resource' => $resource,
                'method' => $method,
                'http' => strtoupper($http),
                'path' => $path,
                'summary' => $op['summary'] ?? '',
                'description' => $op['description'] ?? '',
                'pathParams' => array_values(array_map(static fn (array $p): string => $p['name'], array_filter($params, static fn (array $p): bool => $p['in'] === 'path'))),
                'query' => $query,
                'body' => $op['requestBody']['content']['application/json']['schema'] ?? null,
                'response' => $response,
                'binary' => isset($content['application/octet-stream']),
                'paginated' => (bool) array_filter($query, static fn (array $p): bool => $p['name'] === 'cursor') && isset($props['items'], $props['nextCursor']),
                'idempotent' => !empty($op['x-idempotent']),
                'moneyOut' => !empty($op['x-money-out']),
            ];
        }
    }
    return $out;
}

/** Consulta e corpo juntos, num esquema só (o SDK separa pelo nome). */
function paramsSchema(array $op): ?array
{
    $properties = [];
    $required = [];
    foreach ($op['query'] as $p) {
        $schema = $p['schema'] ?? [];
        if (!empty($p['description'])) {
            $schema['description'] = $p['description'];
        }
        $properties[$p['name']] = $schema;
        if (!empty($p['required'])) {
            $required[] = $p['name'];
        }
    }
    foreach ($op['body']['properties'] ?? [] as $name => $schema) {
        $properties[$name] = $schema;
    }
    foreach ($op['body']['required'] ?? [] as $name) {
        $required[] = $name;
    }
    if (!$properties) {
        return $op['body'] !== null ? ['type' => 'object'] : null;
    }
    return ['type' => 'object', 'properties' => $properties, 'required' => $required];
}

function docBlock(array $lines, string $indent): string
{
    $out = [$indent . '/**'];
    foreach ($lines as $line) {
        $line = str_replace('*/', '* /', (string) $line);
        foreach (explode("\n", $line) as $part) {
            $out[] = rtrim($indent . ' * ' . $part);
        }
    }
    $out[] = $indent . ' */';
    return implode("\n", $out);
}

function exportString(string $value): string
{
    return var_export($value, true);
}

function generate(array $spec): array
{
    $ops = operations($spec);
    $files = [];

    $table = [];
    foreach ($ops as $op) {
        $query = implode(', ', array_map(static fn (array $p): string => exportString($p['name']), $op['query']));
        $table[] = sprintf(
            "        %s => ['method' => %s, 'path' => %s, 'query' => [%s], 'body' => %s, 'idempotent' => %s, 'moneyOut' => %s, 'binary' => %s],",
            exportString($op['id']),
            exportString($op['http']),
            exportString($op['path']),
            $query,
            $op['body'] !== null ? 'true' : 'false',
            $op['idempotent'] ? 'true' : 'false',
            $op['moneyOut'] ? 'true' : 'false',
            $op['binary'] ? 'true' : 'false',
        );
    }
    $files['Operations.php'] = HEADER . "\n" . implode("\n", [
        '/** Cada rota da API: método HTTP, caminho e como o SDK trata o pedido. */',
        'final class Operations',
        '{',
        '    /** @var array<string, array{method: string, path: string, query: list<string>, body: bool, idempotent: bool, moneyOut: bool, binary: bool}> */',
        '    public const ALL = [',
        ...$table,
        '    ];',
        '}',
        '',
    ]);

    $resources = [];
    foreach ($ops as $op) {
        $resources[$op['resource']][] = $op;
    }

    foreach ($resources as $resource => $list) {
        $class = pascal($resource) . 'Resource';
        $methods = [];
        foreach ($list as $op) {
            $params = paramsSchema($op);
            $required = $params && !empty($params['required']);
            $args = array_map(static fn (string $name): string => 'string $' . $name, $op['pathParams']);
            $doc = [$op['summary'], '', ...explode("\n", (string) $op['description']), '', '`' . $op['http'] . ' ' . $op['path'] . '`'];
            $fields = [];
            foreach ($params['properties'] ?? [] as $name => $schema) {
                if (!empty($schema['description'])) {
                    $fields[] = '- `' . $name . '`: ' . str_replace("\n", ' ', (string) $schema['description']);
                }
            }
            if ($fields) {
                array_push($doc, '', 'Campos:', ...$fields);
            }
            $doc[] = '';
            foreach ($op['pathParams'] as $name) {
                $doc[] = '@param string $' . $name . ' O código.';
            }
            if ($params) {
                $args[] = 'array $params' . ($required ? '' : ' = []');
                $doc[] = '@param ' . phpType($params) . ' $params';
            }
            $args[] = 'array $options = []';
            $doc[] = '@param array{' . ($op['idempotent'] ? 'idempotencyKey?: string, ' : '') . 'timeout?: float|int, maxRetries?: int} $options';
            if ($op['binary']) {
                $result = 'string';
                $doc[] = '@return string O arquivo.';
            } elseif ($op['paginated']) {
                $result = '\\WyzeCash\\Page';
                $doc[] = '@return \\WyzeCash\\Page<' . phpType($op['response']['properties']['items']['items'] ?? null) . '>';
            } elseif ($op['response']) {
                $returnType = phpType($op['response']);
                $result = str_starts_with($returnType, 'list<') || str_starts_with($returnType, 'array') ? 'array' : 'mixed';
                $doc[] = '@return ' . $returnType;
            } else {
                $result = 'mixed';
                $doc[] = '@return mixed';
            }
            $doc[] = '@throws \\WyzeCash\\Exception\\WyzeCashException';
            $call = $op['paginated'] ? 'list' : 'request';
            $pathArgs = implode(', ', array_map(static fn (string $name): string => '$' . $name, $op['pathParams']));
            $methods[] = implode("\n", [
                docBlock($doc, '    '),
                '    public function ' . $op['method'] . '(' . implode(', ', $args) . '): ' . $result,
                '    {',
                '        return $this->client->' . $call . '(' . exportString($op['id']) . ', [' . $pathArgs . '], ' . ($params ? '$params' : '[]') . ', $options);',
                '    }',
            ]);
        }
        $files[$class . '.php'] = HEADER . "\n" . implode("\n", [
            'use WyzeCash\\Requester;',
            '',
            'final class ' . $class,
            '{',
            '    public function __construct(private readonly Requester $client)',
            '    {',
            '    }',
            '',
            implode("\n\n", $methods),
            '}',
            '',
        ]);
    }

    $props = [];
    $inits = [];
    foreach (array_keys($resources) as $resource) {
        $class = pascal($resource) . 'Resource';
        $props[] = '    public readonly ' . $class . ' $' . $resource . ';';
        $inits[] = '        $this->' . $resource . ' = new ' . $class . '($client);';
    }
    $files['Resources.php'] = HEADER . "\n" . implode("\n", [
        'use WyzeCash\\Requester;',
        '',
        '/** Os recursos da API, um por grupo de rotas. */',
        'trait Resources',
        '{',
        ...$props,
        '',
        '    private function initResources(Requester $client): void',
        '    {',
        ...$inits,
        '    }',
        '}',
        '',
    ]);

    ksort($files);
    return $files;
}

$root = dirname(__DIR__);
if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === realpath(__FILE__)) {
    $spec = json_decode((string) file_get_contents($root . '/../openapi.json'), true, 512, JSON_THROW_ON_ERROR);
    $files = generate($spec);
    $dir = $root . '/src/Generated';
    if (in_array('--check', $argv, true)) {
        foreach ($files as $name => $content) {
            if (@file_get_contents($dir . '/' . $name) !== $content) {
                fwrite(STDERR, "src/Generated/{$name} está velho: rode php scripts/generate.php\n");
                exit(1);
            }
        }
        exit(0);
    }
    @mkdir($dir, 0777, true);
    foreach (glob($dir . '/*.php') ?: [] as $old) {
        if (!isset($files[basename($old)])) {
            unlink($old);
        }
    }
    foreach ($files as $name => $content) {
        file_put_contents($dir . '/' . $name, $content);
    }
    echo 'src/Generated: ' . count(operations($spec)) . " rotas.\n";
}
