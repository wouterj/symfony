<?php

namespace Symfony\Component\Security\Principal;

use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Only used for backwards compatibility between Principals and Users.
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 *
 * @internal
 * @deprecated since Symfony 4.4, to be removed in Symfony 5.0.
 */
class UserBridgeFactory
{
    public static function createUser(object $principal, array $roles): UserInterface
    {
        return new User(
            $principal instanceof UserInterface
                ? $principal->getUsername()
                : (method_exists($principal, '__toString')
                    ? (string) $principal
                    : spl_object_hash($principal) // @todo replace with getId() when PrincipalInterface exists
                ),
            null,
            $roles
        );
    }
}
