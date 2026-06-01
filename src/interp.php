<?php

/*
Person 1 owns the shared foundation: runtime helpers, AST/value constructors,
environment operations, serialization, error conventions, and the main interp
dispatcher. Person 2 owns primitive operators and the top-level environment.
Person 3 owns function values, closure creation, application, and argument
binding. Person 4 owns literals, identifiers, conditionals, tests, and optional
given desugaring if time allows.

This file is intentionally scaffolded so teammates can work independently in
their assigned sections without overlapping too much.
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
    // Person 3: create a closureV that stores params, body, and the saved env.
    vebgError('function expressions are assigned to Person 3');
}

function interpApp(array $expr, array $env): array
{
    // Person 3: evaluate the function position and arguments, then dispatch to
    // closure application or Person 2's applyPrimop for primitive values.
    vebgError('applications are assigned to Person 3');
}
