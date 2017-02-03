<?php

use App\{ClassA, ClassB, ClassC as C};

use function App\{fn_a, fn_b, fn_c};

use const App\{ConstA, ConstB, ConstC};

function scalarTypeArgument(int ...$ints)
{
    return array_sum($ints);
}

function returnValue(array ...$arrays): array
{
    return array_map(function(array $array): int {
        return array_sum($array);
    }, $arrays);
}

$nullCoalescing = $variable ?? 'default';

$shpaceship = 1 <=> 1;

define('ARRAY_CONSTANT', ['a', 'b', 'c']);

$anonymousClass = new class extends \stdClass {
    public function log(string $msg):string {
        echo $msg;
    }
};
