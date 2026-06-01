# CSC 430 Assignment 8: PHP Interpreter Scaffold

This project is a small PHP scaffold for CSC 430 Assignment 8, "Discovering a New Language." The assigned implementation language is PHP.

The assignment asks the team to redo as much of Assignment 4 as possible in the assigned language, with the interpreter as the important part. This repo intentionally does not include a parser; teammates can build and test programs by manually constructing ASTs.

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

## Notes

- Jarvis hiding/exclusion: no `.jarvisignore`, workspace settings, or similar project-hiding convention was detected under the JARVIS tree, so no hiding rule was added.
- Assignment 4 reference: the local `A4.rkt` file was not available in this environment. This scaffold uses the programming node's Assignment 4 summary and local Racket review notes as style signals instead.
- Source files use the `Vebg` namespace so PHP's built-in `serialize()` function does not conflict with the assignment's expected `serialize($value)` helper.

