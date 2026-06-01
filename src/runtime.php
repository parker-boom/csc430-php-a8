<?php

declare(strict_types=1);

namespace Vebg;

function numE(int|float $n): array
{
    return ['tag' => 'numE', 'n' => $n];
}

function strE(string $s): array
{
    return ['tag' => 'strE', 's' => $s];
}

function boolE(bool $b): array
{
    return ['tag' => 'boolE', 'b' => $b];
}

function idE(string $name): array
{
    ensureName($name, 'identifier');
    return ['tag' => 'idE', 'name' => $name];
}

function ifE(array $test, array $then, array $else): array
{
    return ['tag' => 'ifE', 'test' => $test, 'then' => $then, 'else' => $else];
}

function fnE(array $params, array $body): array
{
    ensureNames($params, 'function parameter');
    return ['tag' => 'fnE', 'params' => array_values($params), 'body' => $body];
}

function appE(array $fn, array $args): array
{
    ensureList($args, 'application argument');
    return ['tag' => 'appE', 'fn' => $fn, 'args' => array_values($args)];
}

function numV(int|float $n): array
{
    return ['tag' => 'numV', 'n' => $n];
}

function strV(string $s): array
{
    return ['tag' => 'strV', 's' => $s];
}

function boolV(bool $b): array
{
    return ['tag' => 'boolV', 'b' => $b];
}

function closureV(array $params, array $body, array $env): array
{
    ensureNames($params, 'closure parameter');
    return [
        'tag' => 'closureV',
        'params' => array_values($params),
        'body' => $body,
        'env' => $env,
    ];
}

function primV(string $name): array
{
    ensureName($name, 'primitive name');
    return ['tag' => 'primV', 'name' => $name];
}

function ensureName(string $name, string $what): void
{
    if ($name === '') {
        vebgError("empty {$what}");
    }
}

function ensureNames(array $names, string $what): void
{
    ensureList($names, $what);

    $seen = [];
    foreach ($names as $name) {
        if (!is_string($name) || $name === '') {
            vebgError("expected non-empty string for {$what}");
        }
        if (array_key_exists($name, $seen)) {
            vebgError("duplicate {$what}: {$name}");
        }
        $seen[$name] = true;
    }
}

function ensureList(array $xs, string $what): void
{
    $expected = 0;
    foreach (array_keys($xs) as $key) {
        if ($key !== $expected) {
            vebgError("expected {$what} list to use consecutive numeric indexes");
        }
        $expected++;
    }
}
