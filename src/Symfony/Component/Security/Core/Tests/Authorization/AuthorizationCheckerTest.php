<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\Tests\Authorization;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\NullToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

class AuthorizationCheckerTest extends TestCase
{
    private $accessDecisionManager;
    private $authorizationChecker;
    private $tokenStorage;

    protected function setUp(): void
    {
        $this->accessDecisionManager = $this->createMock(AccessDecisionManagerInterface::class);
        $this->tokenStorage = new TokenStorage();

        $this->authorizationChecker = new AuthorizationChecker(
            $this->tokenStorage,
            $this->accessDecisionManager
        );
    }

    public function testVoteWithoutAuthenticationToken()
    {
        $this->expectException(AuthenticationCredentialsNotFoundException::class);
        $this->authorizationChecker->isGranted('ROLE_FOO');
    }

    public function testVoteWithoutAuthenticationTokenAndExceptionOnNoTokenIsFalse()
    {
        $authorizationChecker = new AuthorizationChecker($this->tokenStorage, $this->accessDecisionManager, false, false);

        $this->accessDecisionManager
            ->expects($this->once())
            ->method('decide')
            ->with($this->isInstanceOf(NullToken::class))
            ->willReturn(true);

        $this->assertTrue($authorizationChecker->isGranted('ANONYMOUS'));
    }

    /**
     * @dataProvider isGrantedProvider
     */
    public function testIsGranted($decide)
    {
        $token = new UsernamePasswordToken('username', 'password', 'provider', ['ROLE_USER']);

        $this->accessDecisionManager
            ->expects($this->once())
            ->method('decide')
            ->willReturn($decide);
        $this->tokenStorage->setToken($token);
        $this->assertSame($decide, $this->authorizationChecker->isGranted('ROLE_FOO'));
    }

    public function isGrantedProvider()
    {
        return [[true], [false]];
    }

    public function testIsGrantedWithObjectAttribute()
    {
        $attribute = new \stdClass();

        $token = new UsernamePasswordToken('username', 'password', 'provider', ['ROLE_USER']);

        $this->accessDecisionManager
            ->expects($this->once())
            ->method('decide')
            ->with($this->identicalTo($token), $this->identicalTo([$attribute]))
            ->willReturn(true);
        $this->tokenStorage->setToken($token);
        $this->assertTrue($this->authorizationChecker->isGranted($attribute));
    }
}
