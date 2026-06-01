<?php

declare(strict_types=1);

namespace Vebg;

require_once __DIR__ . '/runtime.php';

function initialEnv(): array
{
    // Person 2: extend this with primitive names such as +, -, *, /, <=, equal?,
    // and any assignment-required built-ins once the group settles the surface.
    return emptyEnv();
}

function applyPrimop(string $name, array $args): array
{
    // Person 2: dispatch on $name, validate arity/types, and return a Value.
    vebgError("primitive operator not implemented yet: {$name}");
}
