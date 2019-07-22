<?php

namespace Symfony\Component\Security\Principal\Authentication\Authenticator;

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Principal\Exception\UnsupportedPrincipalException;
use Symfony\Component\Security\Principal\Principal\PasswordPrincipalInterface;
use Symfony\Component\Security\Principal\Principal\PrincipalInterface;
use Symfony\Component\Security\Principal\Token\AuthenticatedTokenInterface;
use Symfony\Component\Security\Principal\Token\AuthenticatedUsernamePasswordToken;

/**
 * @author Wouter de Jong <wouter@wouterj.nl>
 *
 * @property EncoderFactoryInterface $encoderFactory
 */
trait UsernamePasswordTrait
{
    public function checkCredentials($credentials, PrincipalInterface $principal): bool
    {
        if (!$this->encoderFactory instanceof EncoderFactoryInterface) {
            throw new \LogicException(get_class($this).' uses the '.__CLASS__.' trait, which requires an $encoderFactory property to be initialized with an '.EncoderFactoryInterface::class.' implementation.');
        }

        if (!$principal instanceof PasswordPrincipalInterface) {
            throw new UnsupportedPrincipalException('Principal does not implement PasswordPrincipalInterface.');
        }

        if ('' === $credentials['password']) {
            throw new BadCredentialsException('The presented password cannot be empty.');
        }

        if (!$this->encoderFactory->getEncoder($principal)->isPasswordValid($principal->getPassword(), $credentials['password'], null)) {
            throw new BadCredentialsException('The presented password is invalid.');
        }

        return true;
    }

    public function createAuthenticatedToken(PrincipalInterface $principal, string $providerKey): AuthenticatedTokenInterface
    {
        return new AuthenticatedUsernamePasswordToken($principal, $providerKey, $principal->getRoles());
    }
}
