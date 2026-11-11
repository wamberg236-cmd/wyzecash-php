<?php

// Os testes do SDK, sem dependências: php tests/run.php (ou composer test). Cada arquivo *Test.php
// registra os testes com test('nome', fn () => ...). Sai com 1 se algum falhar.

declare(strict_types=1);

error_reporting(E_ALL);
set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
    // O que vem com @ (ex.: fsockopen esperando o servidor subir) não conta.
    if (!(error_reporting() & $severity)) {
        return false;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

spl_autoload_register(static function (string $class): void {
    foreach (['WyzeCash\\Tests\\' => __DIR__ . '/', 'WyzeCash\\' => dirname(__DIR__) . '/src/'] as $prefix => $dir) {
        if (str_starts_with($class, $prefix)) {
            $file = $dir . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
            if (is_file($file)) {
                require $file;
            }
            return;
        }
    }
});

/** @var list<array{string, Closure}> $GLOBALS['tests'] */
$GLOBALS['tests'] = [];

function test(string $name, Closure $fn): void
{
    $GLOBALS['tests'][] = [$name, $fn];
}

final class AssertionFailed extends Exception
{
}

function assertSame(mixed $expected, mixed $actual, string $message = ''): void
{
    if ($expected !== $actual) {
        throw new AssertionFailed(($message !== '' ? $message . ': ' : '') . 'esperava ' . var_export($expected, true) . ', veio ' . var_export($actual, true));
    }
}

function assertTrue(bool $condition, string $message = 'esperava verdadeiro'): void
{
    if (!$condition) {
        throw new AssertionFailed($message);
    }
}

/**
 * @template T of Throwable
 *
 * @param class-string<T> $class
 *
 * @return T
 */
function assertThrows(string $class, Closure $fn): Throwable
{
    try {
        $fn();
    } catch (Throwable $error) {
        if ($error instanceof $class) {
            return $error;
        }
        throw new AssertionFailed('esperava ' . $class . ', veio ' . $error::class . ': ' . $error->getMessage());
    }
    throw new AssertionFailed('esperava ' . $class . ', não lançou nada');
}

$filter = $argv[1] ?? null;
foreach (glob(__DIR__ . '/*Test.php') ?: [] as $file) {
    require $file;
}

$failed = 0;
$ran = 0;
foreach ($GLOBALS['tests'] as [$name, $fn]) {
    if ($filter !== null && !str_contains($name, $filter)) {
        continue;
    }
    $ran++;
    try {
        $fn();
        echo "  ok  {$name}\n";
    } catch (Throwable $error) {
        $failed++;
        echo "  FALHOU  {$name}\n      " . $error::class . ': ' . $error->getMessage() . "\n      " . $error->getFile() . ':' . $error->getLine() . "\n";
    }
}
echo "\n{$ran} testes, " . ($ran - $failed) . " passaram, {$failed} falharam.\n";
exit($failed > 0 ? 1 : 0);
