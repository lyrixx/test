<?php

namespace App\Billing\Stripe;

interface StripeInterface
{
    public function updateStripeWebhookEndpoint(): void;

    public function generateSignatureHeader(string $payload): string;
}
