<?php

namespace Symfony\Component\Security\Principal\Authentication\Provider;


use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Principal\Exception\PrincipalNotFoundException;
use Symfony\Component\Security\Principal\Principal\PrincipalInterface;
use Symfony\Component\Security\Principal\Token\AuthenticatedUsernamePasswordToken;
use Symfony\Component\Security\Principal\Token\UsernamePasswordRequestToken;

/**
 * A base class for username-password authentication (e.g. HTTP basic or login forms).
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
abstract class AbstractUsernamePasswordAuthenticationProvider implements AuthenticationProviderInterface
{
    private $providerKey;
    private $hidePrincipalNotFoundExceptions;

    /**
     * @param string $providerKey                The provider key (often the name of the firewall)
     * @param bool   $hideUserNotFoundExceptions
     */
    public function __construct(string $providerKey, bool $hideUserNotFoundExceptions)
    {
        $this->providerKey = $providerKey;
        $this->hidePrincipalNotFoundExceptions = $hideUserNotFoundExceptions;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof UsernamePasswordRequestToken && $this->providerKey === $token->getProviderKey();
    }

    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            throw new AuthenticationException('The token is not supported by this authentication provider.');
        }

        try {
            /** @var UsernamePasswordRequestToken $token */
            $principal = $this->retrievePrincipal($token);
        } catch (PrincipalNotFoundException $e) {
            if ($this->hidePrincipalNotFoundExceptions) {
                throw new BadCredentialsException('Bad credentials.', 0, $e);
            }
            $e->setUsername($token->getUsername());

            throw $e;
        }

        try {
            $this->checkAuthentication($principal, $token);
        } catch (BadCredentialsException $e) {
            if ($this->hidePrincipalNotFoundExceptions) {
                throw new BadCredentialsException('Bad credentials.', 0, $e);
            }

            throw $e;
        }

        // @todo Switch user functionality with a principal
//        if ($token instanceof SwitchUserToken) {
//            $authenticatedToken = new SwitchUserToken($user, $token->getCredentials(), $this->providerKey, $user->getRoles(), $token->getOriginalToken());
//        } else {
            $authenticatedToken = new AuthenticatedUsernamePasswordToken($principal, $this->providerKey, $principal->getRoles());
//        }

        $authenticatedToken->setAttributes($token->getAttributes(true), true);

        return $authenticatedToken;
    }

    /**
     * Retrieves the principal from an implementation-specific location.
     */
    abstract protected function retrievePrincipal(UsernamePasswordRequestToken $token): PrincipalInterface;

    /**
     * Does additional checks on the token (like validating the credentials).
     *
     * @throws AuthenticationException if the credentials could not be validated
     */
    abstract protected function checkAuthentication(PrincipalInterface $principal, UsernamePasswordRequestToken $token): void;
}
