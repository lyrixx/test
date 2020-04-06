<?php

namespace App\KO;

class ServiceKO
{
    public function __construct()
    {
        $this->microtime();
    }

    public function microtime()
    {
        return microtime(true);
    }
}
