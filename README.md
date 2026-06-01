# CSC 430 Assignment 8: PHP Interpreter Scaffold

This project is a small PHP scaffold for CSC 430 Assignment 8, "Discovering a New Language." The assigned implementation language is PHP.

The assignment asks the team to redo as much of Assignment 4 as possible in the assigned language, with the interpreter as the important part. This repo intentionally does not include a parser; teammates can build and test programs by manually constructing ASTs.

Assignment links:

- Assignment 8: <https://brinckerhoff.org/clements/2264-csc430/Assignments/unseen-language-assignment.html>
- Course page: <https://brinckerhoff.org/clements/2264-csc430/>

## Team Split

- Person 1: foundation/runtime, AST and value constructors, environments, serialization, error conventions, and the interpreter dispatcher.
- Person 2: primitive operators and the top-level environment.
- Person 3: function values, closure creation, application, and argument binding.
- Person 4: literals, identifiers, conditionals, tests, and optional `given` desugaring if time allows.

## Structure

```text
php-a8/
  README.md
  src/
    runtime.php
    primops.php
    interp.php
  tests.php
  demo.php
```

## Running

Prerequisite: PHP CLI must be installed and available as `php`.

Check first:

```sh
php -v
```

No Composer or framework is required after PHP itself is installed.

```sh
php tests.php
php demo.php
```

After cloning:

```sh
git clone https://github.com/parker-boom/csc430-php-a8.git
cd csc430-php-a8
php -v
php tests.php
php demo.php
```

Suggested branch starts:

```sh
git checkout -b person-2-primops
git checkout -b person-3-functions
git checkout -b person-4-literals-tests
```

## Person 1 Complete

- AST constructors: `numE`, `strE`, `boolE`, `idE`, `ifE`, `fnE`, `appE`
- Value constructors: `numV`, `strV`, `boolV`, `closureV`, `primV`
- Environment helpers: `emptyEnv`, `lookup`, `extend`, `extendMany`
- Runtime helpers: `serialize`, `vebgError`
- Interpreter entry points: `interp`, `topInterp`
- Clear dispatch helpers: `interpLiteral`, `interpId`, `interpIf`, `interpFn`, `interpApp`, `applyPrimop`

## Next Work

Person 2 should work in `src/primops.php`:

- Add primitives to `initialEnv()`.
- Implement `applyPrimop($name, $args)`.
- Validate primitive arity and value types.

Person 3 should work in `src/interp.php`:

- Implement `interpFn()` with `closureV($params, $body, $env)`.
- Implement `interpApp()` for closure application.
- Bind arguments with `extendMany()`.
- Dispatch primitive calls to `applyPrimop()`.

Person 4 should work mostly in `src/interp.php` and `tests.php`:

- Finish/expand literal, identifier, and conditional behavior.
- Add tests for all finished language behavior.
- Add optional `given` desugaring only if the team has time.

## Representation

ASTs and runtime values are plain associative arrays with a `tag` field:

```php
\Vebg\numE(4);       // ['tag' => 'numE', 'n' => 4]
\Vebg\numV(4);       // ['tag' => 'numV', 'n' => 4]
\Vebg\idE('+');      // ['tag' => 'idE', 'name' => '+']
```

Environments are lists of bindings, newest first:

```php
$env = \Vebg\extend(\Vebg\emptyEnv(), 'x', \Vebg\numV(10));
\Vebg\lookup($env, 'x');
```

## AI Use Disclosure

This scaffold was created with AI assistance. The local Assignment 4 implementation was not copied into the project. The unavailable local A4 file is noted below.

## Notes

- Jarvis hiding/exclusion: no `.jarvisignore` or workspace settings file was detected, but the parent JARVIS root `.gitignore` now excludes `school/programming/php-a8/` so this nested repo stays out of the parent tree.
- Assignment 4 reference: the local `A4.rkt` file was not available in this environment. This scaffold uses the programming node's Assignment 4 summary and local Racket review notes as style signals instead.
- Source files use the `Vebg` namespace so PHP's built-in `serialize()` function does not conflict with the assignment's expected `serialize($value)` helper.
- PHP availability: modern macOS versions and Windows do not reliably include a `php` CLI by default, so teammates may need to install PHP before running the commands above.
- Known limitation: parser work is intentionally not included. Manually constructed ASTs are the intended testing surface for now.
