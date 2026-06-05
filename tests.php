<?php

declare(strict_types=1);

require_once __DIR__ . '/src/interp.php';

use Vebg\VebgException;
use function Vebg\appE;
use function Vebg\boolE;
use function Vebg\boolV;
use function Vebg\closureV;
use function Vebg\emptyEnv;
use function Vebg\extend;
use function Vebg\extendMany;
use function Vebg\fnE;
use function Vebg\idE;
use function Vebg\ifE;
use function Vebg\interp;
use function Vebg\lookup;
use function Vebg\numE;
use function Vebg\numV;
use function Vebg\primV;
use function Vebg\serialize;
use function Vebg\strE;
use function Vebg\strV;
use function Vebg\topInterp;

function assertSameValue(mixed $expected, mixed $actual, string $label): void
{
    if ($expected !== $actual) {
        throw new Exception(
            "{$label}\nexpected: " . var_export($expected, true) .
            "\nactual:   " . var_export($actual, true)
        );
    }
}

function assertVebgError(callable $thunk, string $contains, string $label): void
{
    try {
        $thunk();
    } catch (VebgException $ex) {
        if (str_contains($ex->getMessage(), $contains)) {
            return;
        }

        throw new Exception("{$label}\nwrong error: {$ex->getMessage()}");
    }

    throw new Exception("{$label}\nexpected VebgException");
}

assertSameValue(['tag' => 'numE', 'n' => 42], numE(42), 'numE constructor');
assertSameValue(['tag' => 'strE', 's' => 'hello'], strE('hello'), 'strE constructor');
assertSameValue(['tag' => 'boolE', 'b' => true], boolE(true), 'boolE constructor');
assertSameValue(['tag' => 'idE', 'name' => 'x'], idE('x'), 'idE constructor');

$conditional = ifE(boolE(true), numE(1), numE(0));
assertSameValue('ifE', $conditional['tag'], 'ifE tag');

$fn = fnE(['x', 'y'], idE('x'));
assertSameValue(['x', 'y'], $fn['params'], 'fnE params');

$app = appE(idE('+'), [numE(1), numE(2)]);
assertSameValue('appE', $app['tag'], 'appE tag');

assertSameValue('42', serialize(numV(42)), 'serialize number');
assertSameValue('"hello"', serialize(strV('hello')), 'serialize string');
assertSameValue('true', serialize(boolV(true)), 'serialize true');
assertSameValue('false', serialize(boolV(false)), 'serialize false');
assertSameValue('#<procedure>', serialize(closureV(['x'], idE('x'), emptyEnv())), 'serialize closure');
assertSameValue('#<procedure>', serialize(primV('+')), 'serialize primitive');

$env = extend(emptyEnv(), 'x', numV(10));
assertSameValue(numV(10), lookup($env, 'x'), 'lookup extended binding');

$env2 = extendMany(emptyEnv(), ['x', 'y'], [numV(1), strV('two')]);
assertSameValue(numV(1), lookup($env2, 'x'), 'extendMany first binding');
assertSameValue(strV('two'), lookup($env2, 'y'), 'extendMany second binding');

assertSameValue('7', topInterp(numE(7)), 'topInterp literal number');
assertSameValue('"ok"', topInterp(strE('ok')), 'topInterp literal string');

assertVebgError(
    fn() => topInterp(appE(primV('+'), [numE(1), numE(2)])),
    'applications are assigned to Person 3',
    'application stub is intentional'
);

assertVebgError(
    fn() => lookup(emptyEnv(), 'missing'),
    'unbound identifier',
    'lookup reports missing identifiers'
);

// Person 4: conditionals. These use boolE literals so they run standalone
assertSameValue('1', topInterp(ifE(boolE(true), numE(1), numE(2))), 'if true picks then');
assertSameValue('2', topInterp(ifE(boolE(false), numE(1), numE(2))), 'if false picks else');
assertSameValue('"yes"', topInterp(ifE(boolE(true), strE('yes'), strE('no'))), 'if over strings');

assertVebgError(
    fn() => topInterp(ifE(numE(0), numE(1), numE(2))),
    'if test not boolean',
    'if requires a boolean test'
);

// Only the selected branch is evaluated: the unbound id in the dead else must not error.
assertSameValue('1', topInterp(ifE(boolE(true), numE(1), idE('missing'))), 'if short-circuits');

// A branch may reference identifiers bound in the environment.
assertSameValue(
    numV(9),
    interp(ifE(boolE(true), idE('x'), numE(0)), extend(emptyEnv(), 'x', numV(9))),
    'if branch resolves identifiers from env'
);

echo "All tests passed\n";
