<?php

namespace Symfony\Component\Security\Principal\Token;

use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use Symfony\Component\Security\Principal\Principal\PrincipalInterface;
use Symfony\Component\Security\Principal\UserBridgeFactory;

/**
 * Used as an "authenticated" token, though it could be set to not-authenticated later.
 *
 * If you're using Guard authentication, you *must* use a class that implements
 * GuardTokenInterface as your authenticated token (like this class).
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 * @author Ryan Weaver <ryan@knpuniversity.com>
 */
class AuthenticatedGuardToken extends PostAuthenticationGuardToken implements AuthenticatedTokenInterface
{
    use DeprecatedTokenMethodsTrait;

    /** @var PrincipalInterface */
    private $principal;

    public function __construct(PrincipalInterface $principal, string $providerKey, array $roles)
    {
        parent::__construct(new User($principal->getId(), null, $roles), $providerKey, $roles);

        $this->principal = $principal;
    }

    public function getPrincipal(): PrincipalInterface
    {
        return $this->principal;
    }
}
