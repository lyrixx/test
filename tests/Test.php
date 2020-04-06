<?php

namespace App;

use App\OK\ServiceOK;
use App\KO\ServiceKO;
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class Test extends WebTestCase
{
    public function testKO()
    {
        $service = new ServiceKO();

        ClockMock::register(ServiceKO::class);
        ClockMock::withClockMock(0);

        $this->assertSame(0.0, $service->microtime());
    }

    public function testOK()
    {
        $service = new ServiceOk();

        ClockMock::register(ServiceOK::class);
        ClockMock::withClockMock(0);

        $this->assertSame(0.0, $service->microtime());
    }
}
