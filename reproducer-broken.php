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
    // From https://github.com/symfony/symfony/blob/master/src/Symfony/Bridge/PhpUnit/ClockMock.php
    // An simplified a lot :)
    class ClockMock
    {
        public static function register(string $ns)
        {
            eval(<<<EOPHP
namespace $ns;

function time()
{
    return 0;
}
EOPHP
            );
        }
    }

    $service = new OK\ServiceOK();
    ClockMock::register('OK');
    var_dump($service->time());

    $service = new KO\ServiceKO();
    ClockMock::register('KO');
    var_dump($service->time());
}
