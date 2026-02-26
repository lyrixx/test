<?php

namespace App\Factory;

use App\Entity\PairingDestination;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<PairingDestination>
 */
final class PairingDestinationFactory extends PersistentObjectFactory
{
    public function __construct() {}

    #[\Override]
    public static function class(): string
    {
        return PairingDestination::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'destinationUrl' => self::faker()->url(),
            'pairing' => PairingFactory::new(),
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(PairingDestination $pairingDestination): void {})
        ;
    }
}
