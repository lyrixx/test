<?php

namespace App\OK;

class ServiceOK
{
    public function __construct()
    {
        microtime(true);
    }

    public function microtime()
    {
        return microtime(true);
    }
}
