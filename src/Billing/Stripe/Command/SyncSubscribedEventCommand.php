<?php

namespace App\Billing\Stripe\Command;

use App\Billing\Stripe\Stripe;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:stripe:synchronize-subscribed-event',
    description: 'Pushes the subscribed event to Stripe',
)]
class SyncSubscribedEventCommand extends Command
{
    public function __construct(
        private readonly Stripe $stripe,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->stripe->updateStripeWebhookEndpoint();

        return Command::SUCCESS;
    }
}
