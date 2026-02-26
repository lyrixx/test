<?php

namespace App\Factory;

use App\Entity\Plan;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Plan>
 */
final class PlanFactory extends PersistentObjectFactory
{
    public function __construct()
    {
    }

    #[\Override]
    public static function class(): string
    {
        return Plan::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->text(),
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this;
        // ->afterInstantiate(function(Plan $plan): void {})
    }
}
