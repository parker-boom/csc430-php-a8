<?php

declare(strict_types=1);

namespace Vebg;

require_once __DIR__ . '/runtime.php';

function initialEnv(): array
{
    // Person 2: extend this with primitive names such as +, -, *, /, <=, equal?,
    // and any assignment-required built-ins once the group settles the surface.
    $env = emptyEnv();
    $env = extendMany(
        $env, 
        ['+', '-', '*', '/', '<=', 'substring', 'strlen', 'equal?', 'true', 'false', 'error'],
        [primV('+'), primV('-'), primV('*'), primV('/'), primV('substring'), primV('strlen'), primV('equal?'), boolV(true), boolV(false), primV('error')]
        );
    return $env;
}

function applyPrimop(string $name, array $args): array
{
    // Person 2: dispatch on $name, validate arity/types, and return a Value.
    return match($name) {
        '+', '-', '*', '/', '<=' => applyBinop($name, $args),
        'substring' => applySubstring($name, $args),
        'strlen' => applyStrlen($name, $args),
        'equal?' => applyEqual($name, $args),
        'error' => count($args) !== 1 ? vebgError("VEBG: {$name} requires one arguement") : vebgError("VEBG: user-error: " . serialize($args[0]))
    };
    vebgError("primitive operator not implemented yet: {$name}");
}

function applySubstring(string $name, array $args) : array
{
    if (count($args) !== 3)  vebgError("VEBG: {$name} requires 3 parameters");
    if ($args[0]['tag'] !== 'strV' || $args[1]['tag'] !== 'numV' || $args[2]['tag'] !== 'numV') vebgError("VEBG: {$name} requires a string, and 2 numbers");
    
    $value = $args[0]['s'];
    $start = $args[1]['n'];
    $stop = $args[2]['n'];

    if (floor($start) !== $start && $start < 0) vebgError("VEBG: {$name} start must be a whole number greater then 0");
    if (floor($stop) !== $stop && $stop < 0) vebgError("VEBG: {$name} stop must be a whole number greater then 0");

    if ($stop < $start) vebgError("VEBG: {$name} requires stop to be before stop");
    if ($start > strlen($value) || $stop > strlen($value)) vebgError("VEBG: {$name} indexes must be in range of value's index");

    return strV(substr($value, (int)$start, (int)($stop - $start)));
}

function applyEqual(string $name, array $args) : array
{
    if (count($args) !== 2)  vebgError("VEBG: {$name} requires 2 parameters");
    if (($args[0]['tag'] === 'closureV' || $args[1]['tag'] === 'closureV' || $args[0]['tag'] === 'primV' || $args[1]['tag'] === 'primV') || $args[0]['tag'] !== $args[1]['tag']) return boolV(false);

    return match($args[0]['tag']) {
        'numV' => boolV($args[0]['n'] === $args[1]['n']),
        'strV' => boolV($args[0]['s'] === $args[1]['s']),
        'boolV' => boolV($args[0]['b'] === $args[1]['b'])
    };
}

function applyStrlen(string $name, array $args) : array
{
    if(count($args) !== 1) vebgError("VEBG: {$name} requires only one parameter");
    if($args[0]['tag'] !== 'strV') vebgError("VEBG: {$name} requires a string");
    return numV(strlen($args[0]['s']));
}

function applyBinop(string $name, array $args) : array
{
    if(count($args) !== 2) vebgError("VEBG: arith functions requires 2 args");
    if($args[0]['tag'] !== 'numV' || $args[1]['tag'] !== 'numV') vebgError("VEBG: {$name} requires 2 numbers");

    return match($name) {
        '+' => numV($args[0]['n'] + $args[1]['n']),
        '-' => numV($args[0]['n'] - $args[1]['n']),
        '*' => numV($args[0]['n'] * $args[1]['n']),
        '/' => $args[1]['n'] === 0 ? vebgError("VEBG: cannot divide by zero") : numV($args[0]['n'] / $args[1]['n']),
        '<=' => boolV($args[0]['n'] <= $args[1]['n'])
    };
}

// basic arith
echo serialize(applyPrimop('+', [numV(3), numV(4)])) . "\n";    // 7
echo serialize(applyPrimop('/', [numV(10), numV(2)])) . "\n";   // 5

// comparisons
echo serialize(applyPrimop('<=', [numV(3), numV(4)])) . "\n";   // true

// strings fun
echo serialize(applyPrimop('strlen', [strV("hello")])) . "\n";  // 5
echo serialize(applyPrimop('substring', [strV("hello"), numV(1), numV(3)])) . "\n";  // "el"

// equal?
echo serialize(applyPrimop('equal?', [numV(3), numV(3)])) . "\n";   // true
echo serialize(applyPrimop('equal?', [numV(3), strV("3")])) . "\n"; // false