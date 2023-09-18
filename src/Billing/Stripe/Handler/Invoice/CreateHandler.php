<?php

namespace App\Billing\Stripe\Handler\Invoice;

use App\Billing\Stripe\Handler\HandlerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Stripe\Event;

class CreateHandler implements HandlerInterface
{
    public function __construct(
        // private readonly Stripe $stripe,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public static function getType(): string
    {
        return 'invoice.created';
    }

    public function handle(Event $event): void
    {
        $this->logger->info('Handling invoice event', [
            'event' => $event,
        ]);
    }
}
