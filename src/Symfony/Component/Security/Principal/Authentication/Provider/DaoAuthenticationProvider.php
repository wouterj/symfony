<?php

namespace Symfony\Component\Security\Principal\Authentication\Provider;

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Principal\Exception\PrincipalNotFoundException;
use Symfony\Component\Security\Principal\Exception\UnsupportedPrincipalException;
use Symfony\Component\Security\Principal\Principal\PasswordPrincipalInterface;
use Symfony\Component\Security\Principal\Principal\PrincipalInterface;
use Symfony\Component\Security\Principal\Principal\PrincipalProviderInterface;
use Symfony\Component\Security\Principal\Token\UsernamePasswordRequestToken;

/**
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class DaoAuthenticationProvider extends AbstractUsernamePasswordAuthenticationProvider
{
    private $principalProvider;
    private $encoderFactory;

    public function __construct(PrincipalProviderInterface $principalProvider, string $providerKey, EncoderFactoryInterface $encoderFactory, bool $hideUserNotFoundExceptions = true)
    {
        parent::__construct($providerKey, $hideUserNotFoundExceptions);

        $this->principalProvider = $principalProvider;
        $this->encoderFactory = $encoderFactory;
    }

    protected function retrievePrincipal(UsernamePasswordRequestToken $token): PrincipalInterface
    {
        try {
            return $this->principalProvider->loadByIdentifier($token->getUsername());
        } catch (PrincipalNotFoundException $e) {
            $e->setUsername($token->getUsername());
            throw $e;
        } catch (\Exception $e) {
            $e = new AuthenticationServiceException($e->getMessage(), 0, $e);
            $e->setToken($token);
            throw $e;
        }
    }

    protected function checkAuthentication(PrincipalInterface $principal, UsernamePasswordRequestToken $token): void
    {
        // @todo the case with $token->getUser() instanceof UserInterface: when does that happen?

        if (!$principal instanceof PasswordPrincipalInterface) {
            throw new UnsupportedPrincipalException('Principal does not implement PasswordPrincipalInterface.');
        }

        if ('' === ($presentedPassword = $token->getPassword())) {
            throw new BadCredentialsException('The presented password cannot be empty.');
        }

        if (!$this->encoderFactory->getEncoder($principal)->isPasswordValid($principal->getPassword(), $presentedPassword, null)) {
            throw new BadCredentialsException('The presented password is invalid.');
        }
    }
}
