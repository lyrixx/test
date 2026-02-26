<?php

namespace App\Story;

use App\Factory\PairingDestinationFactory;
use App\Factory\PairingFactory;
use App\Factory\PlanFactory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

use function Zenstruck\Foundry\faker;

#[AsFixture(name: 'main')]
final class AppStory extends Story
{
    public function build(): void
    {
        $plans = PlanFactory::new()->createMany(10);
        $parings = PairingFactory::new()->createMany(10, fn() => [
            'plan' => faker()->randomElement($plans),
        ]);
        PairingDestinationFactory::new()->createMany(20, fn() => [
            'pairing' => faker()->randomElement($parings),
        ]);

        $plan = PlanFactory::new()->createOne(['id' => '3b78b4d3-f292-4876-85e4-7d5be788d18c']);
        $parings = PairingFactory::new()->createMany(10, fn() => [
            'plan' => $plan,
        ]);
        PairingDestinationFactory::new()->createMany(20, fn() => [
            'pairing' => faker()->randomElement($parings),
        ]);
    }
}
