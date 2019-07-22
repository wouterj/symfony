<?php

namespace Symfony\Component\Security\Principal\Authentication\Authenticator;

use Symfony\Component\Security\Principal\Exception\PrincipalNotFoundException;
use Symfony\Component\Security\Principal\Principal\PrincipalInterface;
use Symfony\Component\Security\Principal\Principal\PrincipalProviderInterface;

/**
 * @author Wouter de Jong <wouter@wouterj.nl>
 *
 * @property PrincipalProviderInterface $principalProvider
 */
trait PrincipalProviderTrait
{
    public function getPrincipal($credentials): ?PrincipalInterface
    {
        if (!$this->principalProvider instanceof PrincipalProviderInterface) {
            throw new \LogicException(get_class($this).' uses the '.__CLASS__.' trait, which requires a $principalProvider property to be initialized with a '.PrincipalProviderInterface::class.' implementation.');
        }

        $principal = $this->principalProvider->loadByIdentifier($credentials['username']);
        if (null === $principal) {
            throw new PrincipalNotFoundException();
        }

        return $principal;
    }
}
