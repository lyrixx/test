<?php

namespace App\Security\Authenticator;

use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use UnexpectedValueException;

class StripeAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        #[Autowire('%env(STRIPE_WEBHOOK_SECRET)%')]
        private readonly string $webhookSecret,
        #[Autowire('%env(STRIPE_TIME_SHIFT_TOLERANCE)%')]
        private readonly int $timeShiftTolerance = Webhook::DEFAULT_TOLERANCE,
    ) {
    }

    public function supports(Request $request): bool
    {
        return true;
    }

    public function authenticate(Request $request): Passport
    {
        $signature = $request->headers->get('stripe-signature', '');

        try {
            $event = Webhook::constructEvent($request->getContent(), $signature, $this->webhookSecret, $this->timeShiftTolerance);
        } catch (UnexpectedValueException|SignatureVerificationException $exception) {
            throw new AuthenticationException($exception->getMessage());
        }

        $request->attributes->set('stripe_event', $event);

        return new SelfValidatingPassport(
            new UserBadge(
                'stripe_user',
                fn () => new InMemoryUser('stripe', null, ['ROLE_API_USER']),
            )
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([], 403);
    }
}
