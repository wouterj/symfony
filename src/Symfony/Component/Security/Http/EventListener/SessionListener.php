<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Http\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\CredentialsValidEvent;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;

/**
 * Migrates the session after successful login.
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class SessionListener implements EventSubscriberInterface
{
    private $sessionAuthenticationStrategy;
    private $statelessProviderKeys;

    public function __construct(SessionAuthenticationStrategyInterface $sessionAuthenticationStrategy, array $statelessProviderKeys = [])
    {
        $this->sessionAuthenticationStrategy = $sessionAuthenticationStrategy;
        $this->statelessProviderKeys = $statelessProviderKeys;
    }

    public static function getSubscribedEvents(): array
    {
        return [CredentialsValidEvent::class => 'onCredentialsValid'];
    }

    public function onCredentialsValid(CredentialsValidEvent $event): void
    {
        $request = $event->getRequest();
        $token = $event->getAuthenticatedToken();
        $providerKey = $event->getProviderKey();

        if (!$request->hasSession() || !$request->hasPreviousSession() || \in_array($providerKey, $this->statelessProviderKeys, true)) {
            return;
        }

        $this->sessionAuthenticationStrategy->onAuthentication($request, $token);
    }
}
