<?php

/*
Shared foundation complete:
runtime helpers, AST/value constructors, environments, serialization, error
conventions, and the main interpreter dispatcher.

Person 2:
implement primitive operators and the top-level environment.

Person 3:
implement function values, closure creation, application, and argument binding.

Person 4:
implement literals, identifiers, conditionals, tests, and optional given
desugaring if time allows.
*/

declare(strict_types=1);

namespace Vebg;

require_once __DIR__ . '/runtime.php';
require_once __DIR__ . '/primops.php';

function interp(array $expr, array $env): array
{
    $tag = $expr['tag'] ?? null;

    return match ($tag) {
        'numE', 'strE', 'boolE' => interpLiteral($expr),
        'idE' => interpId($expr, $env),
        'ifE' => interpIf($expr, $env),
        'fnE' => interpFn($expr, $env),
        'appE' => interpApp($expr, $env),
        default => vebgError('unknown expression tag'),
    };
}

function topInterp(array $expr): string
{
    return serialize(interp($expr, initialEnv()));
}

function interpLiteral(array $expr): array
{
    return match ($expr['tag'] ?? null) {
        'numE' => numV($expr['n']),
        'strE' => strV($expr['s']),
        'boolE' => boolV($expr['b']),
        default => vebgError('expected literal expression'),
    };
}

function interpId(array $expr, array $env): array
{
    return lookup($env, $expr['name']);
}

function interpIf(array $expr, array $env): array
{
    // Person 4: evaluate test, require a boolean value, then interpret the
    // selected branch in the same environment.
    vebgError('if expressions are assigned to Person 4');
}

function interpFn(array $expr, array $env): array
{
    return closureV($expr['params'], $expr['body'], $env);
    // Person 3: create a closureV that stores params, body, and the saved env.
}

function applyClosure(array $closure, array $args): array
{
    $closure['env'] = extendMany($closure['env'], $closure['params'], $args);
    return interp($closure['body'], $closure['env']);
}

function interpApp(array $expr, array $env): array
{
    $fn = interp($expr['fn'], $env);
    $evalArgs = array_map(fn($arg) => interp($arg, $env), $expr['args']);
    return match ($fn['tag'] ?? null) {
        'primV' => applyPrimop($fn['name'], $evalArgs),
        'closureV' => applyClosure($fn, $evalArgs),
        default => vebgError('expected function value in application'),
    };
    // Person 3: evaluate the function position and arguments, then dispatch to
    // closure application or Person 2's applyPrimop for primitive values.
}
