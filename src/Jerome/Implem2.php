<?php

namespace App\Jerome;

class Implem2 implements JeromeInterface
{
    public function getName(): string
    {
        return self::class;
    }

    public static function getNameStoredInDb(): string
    {
        return 'second';
    }
}

