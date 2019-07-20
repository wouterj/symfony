<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\Tests\Authentication\Provider;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\SwitchUserToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Principal\Authentication\Provider\AbstractUsernamePasswordAuthenticationProvider;
use Symfony\Component\Security\Principal\Exception\PrincipalNotFoundException;
use Symfony\Component\Security\Principal\Principal\PasswordPrincipalInterface;
use Symfony\Component\Security\Principal\Principal\PrincipalInterface;
use Symfony\Component\Security\Principal\Token\AuthenticatedUsernamePasswordToken;
use Symfony\Component\Security\Principal\Token\UsernamePasswordRequestToken;

class UserAuthenticationProviderTest extends TestCase
{
    public function testSupports()
    {
        $provider = $this->getProvider();

        $this->assertTrue($provider->supports($this->getSupportedToken()));
        $this->assertFalse($provider->supports($this->getMockBuilder(TokenInterface::class)->getMock()));
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AuthenticationException
     * @expectedExceptionMessage The token is not supported by this authentication provider.
     */
    public function testAuthenticateWhenTokenIsNotSupported()
    {
        $provider = $this->getProvider();

        $provider->authenticate($this->getMockBuilder(TokenInterface::class)->getMock());
    }

    /**
     * @expectedException \Symfony\Component\Security\Principal\Exception\PrincipalNotFoundException
     */
    public function testAuthenticateWhenUsernameIsNotFound()
    {
        $provider = $this->getProvider(false);
        $provider->expects($this->once())
                 ->method('retrievePrincipal')
                 ->willThrowException(new PrincipalNotFoundException())
        ;

        $provider->authenticate($this->getSupportedToken());
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
     */
    public function testAuthenticateWhenUsernameIsNotFoundAndHideIsTrue()
    {
        $provider = $this->getProvider(true);
        $provider->expects($this->once())
                 ->method('retrievePrincipal')
                 ->willThrowException(new PrincipalNotFoundException())
        ;

        $provider->authenticate($this->getSupportedToken());
    }

    public function testAuthenticate()
    {
        $principal = $this->getMockBuilder([PasswordPrincipalInterface::class, PrincipalInterface::class])->getMock();
        $principal->expects($this->once())->method('getRoles')->willReturn(['ROLE_FOO']);
        $principal->expects($this->any())->method('getId')->willReturn('123abc');

        $provider = $this->getProvider();
        $provider->expects($this->once())->method('retrievePrincipal')->willReturn($principal);

        $token = $this->getSupportedToken();

        $authToken = $provider->authenticate($token);

        $this->assertInstanceOf(AuthenticatedUsernamePasswordToken::class, $authToken);
        $this->assertSame($principal, $authToken->getPrincipal());
        $this->assertEquals(['ROLE_FOO'], $authToken->getRoleNames());
        //$this->assertEquals(['foo' => 'bar'], $authToken->getAttributes(), '->authenticate() copies token attributes');
    }

    public function testAuthenticatePreservesOriginalToken()
    {
        $this->markTestSkipped('Switch user functionality not yet supported.');

        $user = $this->getMockBuilder('Symfony\Component\Security\Core\User\UserInterface')->getMock();
        $user->expects($this->once())
             ->method('getRoles')
             ->willReturn(['ROLE_FOO'])
        ;

        $provider = $this->getProvider();
        $provider->expects($this->once())
                 ->method('retrieveUser')
                 ->willReturn($user)
        ;

        $originalToken = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')->getMock();
        $token = new SwitchUserToken($this->getMockBuilder('Symfony\Component\Security\Core\User\UserInterface')->getMock(), 'foo', 'key', [], $originalToken);
        $token->setAttributes(['foo' => 'bar']);

        $authToken = $provider->authenticate($token);

        $this->assertInstanceOf(SwitchUserToken::class, $authToken);
        $this->assertSame($originalToken, $authToken->getOriginalToken());
        $this->assertSame($user, $authToken->getUser());
        $this->assertContains('ROLE_FOO', $authToken->getRoleNames(), '', false, false);
        $this->assertEquals('foo', $authToken->getCredentials());
        $this->assertEquals(['foo' => 'bar'], $authToken->getAttributes(), '->authenticate() copies token attributes');
    }

    protected function getSupportedToken()
    {
        $mock = $this->getMockBuilder(UsernamePasswordRequestToken::class)->setMethods(['getUsername', 'getPassword', 'getProviderKey', 'getRoles'])->disableOriginalConstructor()->getMock();
        $mock
            ->expects($this->any())
            ->method('getProviderKey')
            ->willReturn('key')
        ;
        $mock->expects($this->any())->method('getPassword')->willReturn('foo');

        return $mock;
    }

    protected function getProvider($hide = true)
    {
        return $this->getMockForAbstractClass(AbstractUsernamePasswordAuthenticationProvider::class, ['key', $hide]);
    }
}
