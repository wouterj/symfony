<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Http\Authentication;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;

/**
 * @author Wouter de Jong <wouter@wouterj.nl>
 * @author Ryan Weaver <ryan@knpuniversity.com>
 */
interface AuthenticatorManagerInterface
{
    /**
     * Convenience method to manually login a user and return a
     * Response *if any* for success.
     */
    public function authenticateUser(UserInterface $user, AuthenticatorInterface $authenticator, Request $request): ?Response;

    /**
     * @internal
     */
    public function supports(Request $request): ?bool;

    /**
     * Tries to authenticate the request and returns a response - if any authenticator set one.
     *
     * @internal
     */
    public function authenticateRequest(Request $request): ?Response;

    /**
     * Called whenever there is already is a token, but it needs to be re-authenticated
     * (e.g. in the AuthorizationChecker and AccessListeners).
     *
     * @internal
     */
    public function authenticateToken(TokenInterface $token): TokenInterface;
}
