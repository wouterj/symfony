<?php

namespace Symfony\Bundle\SecurityBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * This contains a map of user providers per firewall.
 *
 * This class is used by some test helpers and should not
 * be used by an application directly.
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 *
 * @internal
 */
final class TestUserProviderMap
{
    private $userProvidersPerFirewall;

    /**
     * @param UserProviderInterface[] $userProviders
     */
    public function addFirewall(string $firewallName, array $userProviders): void
    {
        $this->userProvidersPerFirewall[$firewallName] = $userProviders;
    }

    /**
     * @return UserProviderInterface[]
     */
    public function getFirewall(string $firewallName): array
    {
        return $this->userProvidersPerFirewall[$firewallName] ?? [];
    }
}
