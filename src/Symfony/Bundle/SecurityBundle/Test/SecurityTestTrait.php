<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\SecurityBundle\Test;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SecurityBundle\Security\TestTokenStorage;
use Symfony\Bundle\SecurityBundle\Security\TestUserProviderMap;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Wouter de Jong <wouter@wouterj.nl>
 *
 * @property ContainerInterface|null $container
 */
trait SecurityTestTrait
{
    private function checkTestClass()
    {
        if (!$this instanceof KernelTestCase) {
            throw new \LogicException(__CLASS__.' can only be used in test cases extending '.KernelTestCase::class);
        }

        if (null === self::$container) {
            throw new \LogicException(sprintf('Calling login() before %s is not invalid.', $this instanceof WebTestCase ? 'createClient() or bootKernel()' : 'bootKernel()'));
        }
    }

    /**
     * Authenticate in the session of the Client in this test.
     */
    protected function authenticate(TokenInterface $token, string $firewallName): void
    {
        $this->checkTestClass();

        if (!self::$container->has(TokenStorageInterface::class)) {
            throw new \LogicException('Service '.TokenStorageInterface::class.' does not exists in the test container.');
        }

        TestTokenStorage::setMockedToken($token);
    }

    /**
     * Authenticate a user in the Client used in the test.
     */
    protected function login(string $username, string $firewallName): void
    {
        $this->checkTestClass();

        /** @var TestUserProviderMap $userProviderMap */
        $userProviderMap = self::$container->get('security.test.user_provider_map');

        foreach ($userProviderMap->getFirewall($firewallName) as $userProvider) {
            try {
                $user = $userProvider->loadUserByUsername($username);

                break;
            } catch (UsernameNotFoundException $exception) {
            }
        }

        $this->authenticate(new UsernamePasswordToken($user, null, $firewallName, $user->getRoles()), $firewallName);
    }
}
