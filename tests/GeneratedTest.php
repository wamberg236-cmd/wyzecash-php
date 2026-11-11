<?php

declare(strict_types=1);

use WyzeCash\WyzeCash;

test('src/Generated igual ao que o ../openapi.json gera (rode php scripts/generate.php)', function (): void {
    $spec = dirname(__DIR__, 2) . '/openapi.json';
    if (!is_file($spec)) {
        echo "      (pulado: sem o ../openapi.json, fora do repositório do backend)\n";
        return;
    }
    $output = [];
    exec(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(dirname(__DIR__) . '/scripts/generate.php') . ' --check 2>&1', $output, $code);
    assertSame(0, $code, implode("\n", $output));
});

test('todo método do catálogo existe no cliente', function (): void {
    $spec = dirname(__DIR__, 2) . '/openapi.json';
    if (!is_file($spec)) {
        return;
    }
    $data = json_decode((string) file_get_contents($spec), true, 512, JSON_THROW_ON_ERROR);
    $wyzecash = new WyzeCash('bk_live_teste');
    $count = 0;
    foreach ($data['paths'] as $methods) {
        foreach ($methods as $op) {
            [$resource, $method] = explode('.', $op['x-sdk-method']);
            assertTrue(method_exists($wyzecash->{$resource}, $method), $op['x-sdk-method']);
            $count++;
        }
    }
    assertSame(48, $count);
});
