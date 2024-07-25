<?php

namespace App\Jerome;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag()]
interface JeromeInterface
{
    public function getName(): string;

    public static function getNameStoredInDb(): string;
}
