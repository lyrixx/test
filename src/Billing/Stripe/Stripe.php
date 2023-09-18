<?php

namespace App\Billing\Stripe;

use App\Billing\Stripe\Handler\HandlerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Stripe\Stripe as StripeClient;
use Stripe\WebhookEndpoint;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

#[AsAlias()]
class Stripe implements StripeInterface
{
    private readonly bool $enabled;

    public function __construct(
        #[Autowire('%env(STRIPE_SECRET_KEY)%')]
        string $secretKey,
        #[Autowire('%env(STRIPE_WEBHOOK_SECRET)%')]
        private readonly string $webhookSecret,
        #[Autowire('%env(STRIPE_WEBHOOK_ID)%')]
        private readonly string $webhookId,
        #[TaggedIterator(HandlerInterface::class, defaultIndexMethod: 'getType')]
        private readonly iterable $handlers,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {
        StripeClient::setApiVersion('2023-08-16');

        $this->enabled = $secretKey !== '';
        if ($this->enabled) {
            StripeClient::setApiKey($secretKey);
        }
    }

    public function updateStripeWebhookEndpoint(): void
    {
        $this->logger->debug('Updating Stripe webhook endpoint.');

        $events = [];
        foreach ($this->handlers as $handler) {
            $events[] = $handler::getType();
        }

        WebhookEndpoint::update($this->webhookId, [
            'enabled_events' => $events,
        ]);
    }

    public function generateSignatureHeader(string $payload): string
    {
        $timestamp = time();
        $signedPayload = "{$timestamp}.{$payload}";
        $signature = hash_hmac('sha256', $signedPayload, $this->webhookSecret);

        return "t={$timestamp},v1={$signature}";
    }
}
