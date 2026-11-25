<?php

namespace Acme\Docker;

final class Compose
{
    public static function command(string ...$args): string
    {
        return implode(' ', ['docker', 'compose', ...$args]);
    }
}
