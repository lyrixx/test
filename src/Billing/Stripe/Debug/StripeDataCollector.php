<?php

namespace App\Billing\Stripe\Debug;

use App\Billing\Stripe\StripeInterface;
use Stripe\StripeObject;
use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

#[When('dev')]
#[When('test')]
#[AsDecorator(StripeInterface::class)]
#[AutoconfigureTag('data_collector', ['id' => StripeDataCollector::class])]
class StripeDataCollector extends AbstractDataCollector implements StripeInterface
{
    public function __construct(
        private readonly StripeInterface $stripe,
    ) {
    }

    public function updateStripeWebhookEndpoint(): void
    {
        $this->callInner(__FUNCTION__, \func_get_args());
    }

    public function generateSignatureHeader(string $payload): string
    {
        return $this->callInner(__FUNCTION__, \func_get_args());
    }

    public function collect(Request $request, Response $response, Throwable $exception = null): void
    {
    }

    public static function getTemplate(): ?string
    {
        return 'collector/stripe/stripe.html.twig';
    }

    public function getCalls(): array
    {
        return $this->data['calls'] ?? [];
    }

    private function callInner(string $method, array $args): mixed
    {
        $startAt = microtime(true);
        try {
            return $return = $this->stripe->{$method}(...$args);
        } catch (Throwable $exception) {
            throw $exception;
        } finally {
            $call = [
                'method' => debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'],
                'duration' => microtime(true) - $startAt,
            ];
            if (isset($exception)) {
                $call['exception'] = $this->cloneVar($exception);
            } elseif (($return ?? null) instanceof StripeObject) {
                $call['returnStripeObject'] = $this->cloneVar($return);
                $call['returnStripeObjectAsJson'] = $return->toJSON();
            } elseif ($return ?? null) {
                $call['return'] = $this->cloneVar($return);
            }

            $this->data['calls'][] = $call;
        }
    }
}
