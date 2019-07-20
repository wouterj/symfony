<?php

namespace Symfony\Component\Security\Principal\Principal;

/**
 * An entity that can be authenticated by the application.
 *
 * This can be for instance a user, an API token or any
 * other entity (e.g. server).
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
interface PrincipalInterface
{
    /**
     * @return string a unique identifier for this principal (e.g. email, MAC address, signature)
     */
    public function getId(): string;

    /**
     * @return string[] a list of roles for this principal
     */
    public function getRoles(): array;
}
