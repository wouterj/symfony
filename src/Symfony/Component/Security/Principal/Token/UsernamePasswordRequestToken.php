<?php

namespace Symfony\Component\Security\Principal\Token;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Represents an authentication request using a username and password.
 *
 * This is used in traditional logins with for instance email + password.
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 * @author Fabien Potencier <fabien@symfony.com>
 */
class UsernamePasswordRequestToken extends UsernamePasswordToken implements AuthenticationRequestTokenInterface
{
    use DeprecatedTokenMethodsTrait;

    /** @var string */
    private $username;
    /** @var string */
    private $password;

    /**
     * @param string $username    the username used during authentication (e.g. an emailadres or system ID)
     * @param string $password    the password entered during authentication
     * @param string $providerKey the provider key (often a HTTP firewall)
     */
    public function __construct(string $username, ?string $password, string $providerKey)
    {
        $this->username = $username;
        $this->password = $password;

        parent::__construct($username, $password, $providerKey);
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }
}
