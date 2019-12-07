<?php

namespace Symfony\Bundle\SecurityBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Contracts\Service\ResetInterface;

/**
 * A decorator of the TokenStorage used to login in tests.
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class TestTokenStorage extends TokenStorage
{
    private $tokenStorage;

    private static $mockedToken;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public static function setMockedToken(TokenInterface $token)
    {
        self::$mockedToken = $token;
    }

    public function getToken()
    {
        $currentToken = $this->tokenStorage->getToken();
        if (null === $currentToken) {
            $this->tokenStorage->setToken($currentToken = self::$mockedToken);
        }

        return $currentToken;
    }

    public function setToken(TokenInterface $token = null)
    {
        self::$mockedToken = null;
        $this->tokenStorage->setToken($token);
    }

    public function setInitializer(?callable $initializer): void
    {
        if (!method_exists($this->tokenStorage, 'setInitialize')) {
            throw new \BadMethodCallException('Called undefined method '.get_class($this->tokenStorage).'::setInitializer()');
        }

        $this->tokenStorage->setInitialize($initializer);
    }

    public function reset()
    {
        if ($this->tokenStorage instanceof ResetInterface) {
            $this->tokenStorage->reset();
        }
    }
}
