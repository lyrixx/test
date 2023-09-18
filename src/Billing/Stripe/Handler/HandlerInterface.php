<?php

namespace App\Billing\Stripe\Handler;

use Stripe\Event;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag()]
interface HandlerInterface
{
    public static function getType(): string;

    public function handle(Event $event): void;
}
