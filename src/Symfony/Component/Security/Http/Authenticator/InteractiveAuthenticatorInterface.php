<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Http\Authenticator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * This is an extension of the authenticator interface that must
 * be used by interactive authenticators.
 *
 * Interactive login requires explicit user action (e.g. a login
 * form or HTTP basic authentication).
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
interface InteractiveAuthenticatorInterface extends AuthenticatorInterface
{
    /**
     * Called when authentication executed and was successful!
     *
     * This should return the Response sent back to the user, like a
     * RedirectResponse to the last page they visited.
     *
     * If you return null, the current request will continue, and the user
     * will be authenticated. This makes sense, for example, with an API.
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): ?Response;
}
