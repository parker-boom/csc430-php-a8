<?php

declare(strict_types=1);

require_once __DIR__ . '/src/interp.php';

use function Vebg\emptyEnv;
use function Vebg\extend;
use function Vebg\idE;
use function Vebg\lookup;
use function Vebg\numE;
use function Vebg\numV;
use function Vebg\serialize;
use function Vebg\strV;
use function Vebg\topInterp;

echo "CSC 430 PHP A8 demo\n";

$expr = numE(430);
$value = numV(430);
$env = extend(emptyEnv(), 'course', strV('CSC 430'));

echo "AST:\n";
echo json_encode($expr, JSON_PRETTY_PRINT) . "\n\n";

echo "Serialized value: " . serialize($value) . "\n";
echo "Serialized env lookup: " . serialize(lookup($env, 'course')) . "\n";
echo "topInterp(numE(430)): " . topInterp($expr) . "\n";
echo "Identifier AST example:\n";
echo json_encode(idE('course'), JSON_PRETTY_PRINT) . "\n";
