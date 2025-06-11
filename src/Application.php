<?php

namespace App;

class Application
{
    public function __construct(
        private Foo $foo,
        private string $name = 'my-app',
    ) {}

    public function getName(): string
    {
        return $this->name;
    }
}
