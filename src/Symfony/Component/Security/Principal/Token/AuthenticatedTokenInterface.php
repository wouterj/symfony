<?php

namespace Symfony\Component\Security\Principal\Token;

use Symfony\Component\Security\Core\User\UserInterface;

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
     * @return UserInterface|object the authenticated principal
     */
    public function getPrincipal(): object;

    /**
     * @return string[] list of roles of the authenticated principal
     */
    public function getRoleNames(): array;
}
