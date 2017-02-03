<?php

function nullableTypes(?string $a): ?string {
    return $a;
}

function voidFunction(): void {
}


function iterableFunction(iterable $iter): iterable
{
    return $iter;
}

$arrayDestructing = [
    [1, 'Tom'],
    [2, 'Fred'],
];

[$id1, $name1] = $arrayDestructing[0];

foreach ($arrayDestructing as [$id, $name]) {
}

// Keys in array destructing
$data = [
    ['id' => 1, 'name' => 'Tom'],
    ['id' => 2, 'name' => 'Fred'],
];

list('id' => $id1, 'name' => $name1) = $data[0];
['id' => $id1, 'name' => $name1] = $data[0];

foreach ($data as list('id' => $id, 'name' => $name)) {
}

foreach ($data as ['id' => $id, 'name' => $name]) {
}


class ConstantVisibility
{
    const PUBLIC_CONST_A = 1;
    public const PUBLIC_CONST_B = 2;
    protected const PROTECTED_CONST = 3;
    private const PRIVATE_CONST = 4;
}


// Multiple catch
try {
    throw new RuntimeException();
} catch (RuntimeException | LogicException $e) {
}


$negativeOffset = 'abcdef'[-2];
