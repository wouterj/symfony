<?php

namespace Symfony\Component\Security\Principal\Token;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Principal\Principal\PrincipalInterface;
use Symfony\Component\Security\Principal\UserBridgeFactory;

/**
 * @author Wouter de Jong <wouter@wouterj.nl>
 * @author Fabien Potencier <fabien@symfony.com>
 */
class AuthenticatedUsernamePasswordToken extends UsernamePasswordToken implements AuthenticatedTokenInterface
{
    use DeprecatedTokenMethodsTrait;

    /** @var object */
    private $principal;

    /**
     * @param \Symfony\Component\Security\Principal\Principal\PrincipalInterface $principal   the authenticated principal
     * @param string                                                             $providerKey the provider key (often a HTTP firewall)
     * @param array                                                              $roles       list of roles of the principal
     */
    public function __construct(PrincipalInterface $principal, string $providerKey, array $roles, $credentials = null)
    {
        if (null !== $credentials) {
            @trigger_error('Parameter 4 (credentials) of '.__METHOD__.' is deprecated since Symfony 4.4 and will be removed in 5.0.', E_USER_DEPRECATED);
        }

        parent::__construct(new User($principal->getId(), null, $roles), $credentials, $providerKey, $roles);

        $this->principal = $principal;
    }

    public function getPrincipal(): PrincipalInterface
    {
        return $this->principal;
    }
}
