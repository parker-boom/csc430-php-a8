<?php

/*
Shared runtime definitions live here. Feel free to append new AST/value helpers
or runtime checks below as the interpreter grows.
*/

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

function emptyEnv(): array
{
    return [];
}

function lookup(array $env, string $name): array
{
    ensureName($name, 'lookup name');

    foreach ($env as $binding) {
        if (!is_array($binding) || !array_key_exists('name', $binding) || !array_key_exists('value', $binding)) {
            vebgError('malformed environment binding');
        }
        if ($binding['name'] === $name) {
            return $binding['value'];
        }
    }

    vebgError("unbound identifier: {$name}");
}

function extend(array $env, string $name, array $value): array
{
    ensureName($name, 'binding name');

    $newEnv = $env;
    array_unshift($newEnv, ['name' => $name, 'value' => $value]);
    return $newEnv;
}

function extendMany(array $env, array $names, array $values): array
{
    ensureNames($names, 'binding name');
    ensureList($values, 'binding value');

    if (count($names) !== count($values)) {
        vebgError('wrong number of arguments');
    }

    $newEnv = $env;
    for ($i = count($names) - 1; $i >= 0; $i--) {
        $newEnv = extend($newEnv, $names[$i], $values[$i]);
    }

    return $newEnv;
}

function serialize(array $value): string
{
    $tag = $value['tag'] ?? null;

    return match ($tag) {
        'numV' => formatNumber($value['n']),
        'strV' => json_encode($value['s'], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
        'boolV' => $value['b'] ? 'true' : 'false',
        'closureV', 'primV' => '#<procedure>',
        default => vebgError('cannot serialize malformed value'),
    };
}

function vebgError(string $message): never
{
    throw new VebgException("VEBG error: {$message}");
}

function formatNumber(int|float $n): string
{
    if (is_int($n) || floor($n) == $n) {
        return (string) (int) $n;
    }

    return rtrim(rtrim(sprintf('%.14F', $n), '0'), '.');
}

class VebgException extends \RuntimeException
{
}
