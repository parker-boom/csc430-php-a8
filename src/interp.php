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

