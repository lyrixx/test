<?php

namespace App\Factory;

use App\Entity\Pairing;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Pairing>
 */
final class PairingFactory extends PersistentObjectFactory
{
    public function __construct() {}

    #[\Override]
    public static function class(): string
    {
        return Pairing::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'plan' => PlanFactory::new(),
            'sourceUrl' => self::faker()->url(),
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Pairing $pairing): void {})
        ;
    }
}
