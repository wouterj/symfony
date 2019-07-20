<?php

namespace Symfony\Component\Security\Principal\Principal;

use Symfony\Component\Security\Principal\Exception\PrincipalNotFoundException;
use Symfony\Component\Security\Principal\Exception\UnsupportedPrincipalException;

/**
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
interface PrincipalProviderInterface
{
    /**
     * Provides a principle based on an identifier (e.g. user email).
     *
     * This identifier is retrieved from the AuthenticationRequestTokenInterface
     * that is authenticating with the application.
     *
     * @throws PrincipalNotFoundException if a principal with this identifier is not found
     */
    public function loadByIdentifier(string $identifier): PrincipalInterface;

    /**
     * Refreshes an already authenticated principal.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @throws UnsupportedPrincipalException if the principal is not supported
     * @throws PrincipalNotFoundException    if a principal with this identifier is not found
     */
    public function refreshPrincipal(PrincipalInterface $principal): PrincipalInterface;
}
