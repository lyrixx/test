<?php

namespace App\Billing\Stripe\Controller;

use App\Billing\Stripe\Handler\HandlerInterface;
use LogicException;
use Stripe\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/stripe/webhook', name: 'stripe_webhook')]
class WebhookController extends AbstractController
{
    public function __construct(
        #[TaggedLocator(HandlerInterface::class, defaultIndexMethod: 'getType')]
        private readonly ServiceLocator $handlers,
    ) {
    }

    public function __invoke(Request $request)
    {
        $event = $request->attributes->get('stripe_event');

        if (! $event instanceof Event) {
            throw new LogicException("The 'stripe_event' attribute must be an instance of " . Event::class);
        }

        if (! $this->handlers->has($event->type)) {
            return new JsonResponse([], 404);
        }

        $this->handlers->get($event->type)->handle($event);

        return new JsonResponse([], 200);
    }
}
