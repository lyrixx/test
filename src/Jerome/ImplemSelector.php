<?php

namespace App\Jerome;

use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;
use App\Jerome\JeromeInterface;

class ImplemSelector
{
    public function __construct(
        #[TaggedLocator(JeromeInterface::class, defaultIndexMethod: 'getNameStoredInDb')]
        private readonly ServiceLocator $serviceLocator
    ) {
    }

    public function selectImplem(): JeromeInterface
    {
        // Find the name in DB.
        // Here I fake
        $name = ['first', 'second'][random_int(0, 1)];

        return $this->serviceLocator->get($name);
    }
}
