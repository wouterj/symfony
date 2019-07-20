<?php

namespace Symfony\Component\Security\Principal\Principal;

/**
 * Represents a principal that authenticates based on a password.
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
interface PasswordPrincipalInterface
{
    public function getPassword(): string;
}
