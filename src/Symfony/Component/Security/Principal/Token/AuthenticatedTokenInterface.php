<?php

namespace Symfony\Component\Security\Principal\Token;

use Symfony\Component\Security\Principal\Principal\PrincipalInterface;

/**
 * Holds the authenticated data needed by the Security component.
 *
 * This token is created by the {@see AuthenticationProviderInterface},
 * based on the {@see AuthenticationRequestTokenInterface}.
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
interface AuthenticatedTokenInterface
{
    /**
     * @return \Symfony\Component\Security\Principal\Principal\PrincipalInterface the authenticated principal
     */
    public function getPrincipal(): PrincipalInterface;

    /**
     * @return string[] list of roles of the authenticated principal
     */
    public function getRoleNames(): array;
}
