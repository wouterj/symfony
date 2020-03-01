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

use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * This class is a bridge between the old AuthenticationManagerInterface
 * and the new AuthenticatorManagerInterface.
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class LegacyAuthenticationManager implements AuthenticationManagerInterface
{
    private $authenticatorManager;

    public function __construct(AuthenticatorManagerInterface $authenticatorManager)
    {
        $this->authenticatorManager = $authenticatorManager;
    }

    public function authenticate(TokenInterface $token)
    {
        return $this->authenticatorManager->authenticateToken($token);
    }
}
