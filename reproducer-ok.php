<?php

namespace OK
{
    class ServiceOK
    {
        public function __construct()
        {
            time();
        }

        public function time()
        {
            return time();
        }
    }
}

namespace KO
{
    class ServiceKO
    {
        public function __construct()
        {
            $this->time();
        }

        public function time()
        {
            return time();
        }
    }
}

namespace {
    $serviceOK = new OK\ServiceOK();
    $serviceKO = new KO\ServiceKO();

    eval(<<<EOPHP
namespace OK;

function time()
{
    return 0;
}
EOPHP);

    eval(<<<EOPHP
namespace KO;

function time()
{
    return 0;
}
EOPHP);

    var_dump($serviceOK->time());
    var_dump($serviceKO->time());
}
