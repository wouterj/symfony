<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\SecurityBundle\Security;

use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\LogicException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticatorManagerInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;

/**
 * A decorator that delegates all method calls to the authenticator
 * manager of the current firewall.
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class FirewallAwareAuthenticatorManager implements AuthenticatorManagerInterface
{
    private $firewallMap;
    private $authenticatorManagers;
    private $requestStack;

    public function __construct(FirewallMap $firewallMap, ServiceLocator $authenticatorManagers, RequestStack $requestStack)
    {
        $this->firewallMap = $firewallMap;
        $this->authenticatorManagers = $authenticatorManagers;
        $this->requestStack = $requestStack;
    }

    public function authenticateUser(UserInterface $user, AuthenticatorInterface $authenticator, Request $request): ?Response
    {
        return $this->getAuthenticatorManager()->authenticateUser($user, $authenticator, $request);
    }

    public function supports(Request $request): ?bool
    {
        return $this->getAuthenticatorManager()->supports($request);
    }

    public function authenticateRequest(Request $request): ?Response
    {
        return $this->getAuthenticatorManager()->authenticateRequest($request);
    }

    public function authenticateToken(TokenInterface $token): TokenInterface
    {
        return $this->getAuthenticatorManager()->authenticateToken($token);
    }

    private function getAuthenticatorManager(): AuthenticatorManagerInterface
    {
        $firewallConfig = $this->firewallMap->getFirewallConfig($this->requestStack->getMasterRequest());
        if (null === $firewallConfig) {
            throw new LogicException('Cannot call authenticate on this request, as it is not behind a firewall.');
        }

        return $this->authenticatorManagers->get($firewallConfig->getName());
    }
}
