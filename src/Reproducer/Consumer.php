<?php

namespace App\Reproducer;

use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;

class Foo
{
    public function __construct(
        #[AutowireLocator(MyInterface::class)]
        private ContainerInterface $container,
    ) {}

    public function consume(): void
    {
        $this->container->get(Foo::class);
    }
}
