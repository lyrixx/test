<?php

namespace App;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

class Foo
{
    public function __construct(
        #[Autowire(lazy: true)]
        private Application $application,
    ) {}

    public function getApplicationName(): string
    {
        return $this->application->getName();
    }
}
