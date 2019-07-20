<?php

namespace Symfony\Component\Security\Principal\Tests\Authentication\Provider;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Principal\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Principal\Exception\PrincipalNotFoundException;
use Symfony\Component\Security\Principal\Principal\PasswordPrincipalInterface;
use Symfony\Component\Security\Principal\Principal\PrincipalInterface;
use Symfony\Component\Security\Principal\Principal\PrincipalProviderInterface;
use Symfony\Component\Security\Principal\Token\UsernamePasswordRequestToken;

class DaoAuthenticationProviderTest extends TestCase
{
    /**
     * @expectedException \Symfony\Component\Security\Principal\Exception\PrincipalNotFoundException
     */
    public function testRetrievePrincipalWhenPrincipalIsNotFound()
    {
        $principalProvider = $this->getMockBuilder(PrincipalProviderInterface::class)->getMock();
        $principalProvider->expects($this->once())
            ->method('loadByIdentifier')
            ->willThrowException(new PrincipalNotFoundException())
        ;

        $provider = $this->getProvider(null, null, $principalProvider);
        $method = new \ReflectionMethod($provider, 'retrievePrincipal');
        $method->setAccessible(true);

        $method->invoke($provider, $this->getSupportedToken());
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AuthenticationServiceException
     */
    public function testRetrievePrincipalWhenAnExceptionOccurs()
    {
        $principalProvider = $this->getMockBuilder(PrincipalProviderInterface::class)->getMock();
        $principalProvider->expects($this->once())
            ->method('loadByIdentifier')
            ->willThrowException(new \RuntimeException())
        ;

        $provider = $this->getProvider(null, null, $principalProvider);
        $method = new \ReflectionMethod($provider, 'retrievePrincipal');
        $method->setAccessible(true);

        $method->invoke($provider, $this->getSupportedToken());
    }

    public function testRetrieveUser()
    {
        $user = $this->getMockBuilder(PrincipalInterface::class)->getMock();

        $principalProvider = $this->getMockBuilder(PrincipalProviderInterface::class)->getMock();
        $principalProvider->expects($this->once())
            ->method('loadByIdentifier')
            ->willReturn($user)
        ;

        $provider = $this->getProvider(null, null, $principalProvider);
        $method = new \ReflectionMethod($provider, 'retrievePrincipal');
        $method->setAccessible(true);

        $this->assertSame($user, $method->invoke($provider, $this->getSupportedToken()));
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
     */
    public function testCheckAuthenticationWhenPasswordIsEmpty()
    {
        $encoder = $this->getMockBuilder(PasswordEncoderInterface::class)->getMock();
        $encoder->expects($this->never())->method('isPasswordValid');

        $provider = $this->getProvider(null, $encoder);
        $method = new \ReflectionMethod($provider, 'checkAuthentication');
        $method->setAccessible(true);

        $token = $this->getSupportedToken();
        $token
            ->expects($this->once())
            ->method('getPassword')
            ->willReturn('')
        ;

        $method->invoke(
            $provider,
            $this->getMockBuilder([PrincipalInterface::class, PasswordPrincipalInterface::class])->getMock(),
            $token
        );
    }

    public function testCheckAuthenticationWhenPasswordIs0()
    {
        $encoder = $this->getMockBuilder(PasswordEncoderInterface::class)->getMock();
        $encoder
            ->expects($this->once())
            ->method('isPasswordValid')
            ->willReturn(true)
        ;

        $provider = $this->getProvider(null, $encoder);
        $method = new \ReflectionMethod($provider, 'checkAuthentication');
        $method->setAccessible(true);

        $token = $this->getSupportedToken();
        $token
            ->expects($this->once())
            ->method('getPassword')
            ->willReturn('0')
        ;

        $method->invoke(
            $provider,
            $this->getMockBuilder([PasswordPrincipalInterface::class, PrincipalInterface::class])->getMock(),
            $token
        );
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
     */
    public function testCheckAuthenticationWhenPasswordIsNotValid()
    {
        $encoder = $this->getMockBuilder(PasswordEncoderInterface::class)->getMock();
        $encoder->expects($this->once())
            ->method('isPasswordValid')
            ->willReturn(false)
        ;

        $provider = $this->getProvider(null, $encoder);
        $method = new \ReflectionMethod($provider, 'checkAuthentication');
        $method->setAccessible(true);

        $token = $this->getSupportedToken();
        $token->expects($this->once())
            ->method('getPassword')
            ->willReturn('foo')
        ;

        $method->invoke($provider, $this->getMockBuilder([PasswordPrincipalInterface::class, PrincipalInterface::class])->getMock(), $token);
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
     */
    public function testCheckAuthenticationDoesNotReauthenticateWhenPasswordHasChanged()
    {
        $this->markTestSkipped('Authenticating an authenticated token is not supported yet.');

        $principal = $this->getMockBuilder([PasswordPrincipalInterface::class, PrincipalInterface::class])->getMock();
        $principal->expects($this->once())
            ->method('getPassword')
            ->willReturn('foo')
        ;

        $token = $this->getSupportedToken();
        $token->expects($this->once())
            ->method('getPrincipal')
            ->willReturn($principal);

        $dbPrincipal = $this->getMockBuilder([PasswordPrincipalInterface::class, PrincipalInterface::class])->getMock();
        $dbPrincipal->expects($this->once())
            ->method('getPassword')
            ->willReturn('newFoo')
        ;

        $provider = $this->getProvider();
        $reflection = new \ReflectionMethod($provider, 'checkAuthentication');
        $reflection->setAccessible(true);
        $reflection->invoke($provider, $dbPrincipal, $token);
    }

    public function testCheckAuthenticationWhenTokenNeedsReauthenticationWorksWithoutOriginalCredentials()
    {
        $this->markTestSkipped('Authenticating an authenticated token is not supported yet.');

        $principal = $this->getMockBuilder([PasswordPrincipalInterface::class, PrincipalInterface::class])->getMock();
        $principal->expects($this->once())
            ->method('getPassword')
            ->willReturn('foo')
        ;

        $token = $this->getSupportedToken();
        $token->expects($this->once())
            ->method('getPrincipal')
            ->willReturn($principal);

        $dbPrincipal = $this->getMockBuilder([PasswordPrincipalInterface::class, PrincipalInterface::class])->getMock();
        $dbPrincipal->expects($this->once())
            ->method('getPassword')
            ->willReturn('foo')
        ;

        $provider = $this->getProvider();
        $reflection = new \ReflectionMethod($provider, 'checkAuthentication');
        $reflection->setAccessible(true);
        $reflection->invoke($provider, $dbPrincipal, $token);
    }

    public function testCheckAuthentication()
    {
        $encoder = $this->getMockBuilder(PasswordEncoderInterface::class)->getMock();
        $encoder->expects($this->once())
            ->method('isPasswordValid')
            ->willReturn(true)
        ;

        $provider = $this->getProvider(null, $encoder);
        $method = new \ReflectionMethod($provider, 'checkAuthentication');
        $method->setAccessible(true);

        $token = $this->getSupportedToken();
        $token->expects($this->once())
            ->method('getPassword')
            ->willReturn('foo')
        ;

        $method->invoke($provider, $this->getMockBuilder([PasswordPrincipalInterface::class, PrincipalInterface::class])->getMock(), $token);
    }

    protected function getSupportedToken()
    {
        $mock = $this->getMockBuilder(UsernamePasswordRequestToken::class)->setMethods(['getPassword', 'getUsername', 'getProviderKey'])->disableOriginalConstructor()->getMock();
        $mock
            ->expects($this->any())
            ->method('getProviderKey')
            ->willReturn('key')
        ;

        return $mock;
    }

    protected function getProvider($user = null, $passwordEncoder = null, $principalProvider = null)
    {
        if (null === $principalProvider) {
            $principalProvider = $this->getMockBuilder(PrincipalProviderInterface::class)->getMock();
        }

        if (null !== $user) {
            $principalProvider->expects($this->once())
                ->method('loadByIdentifier')
                ->willReturn($user)
            ;
        }

        if (null === $passwordEncoder) {
            $passwordEncoder = new PlaintextPasswordEncoder();
        }

        $encoderFactory = $this->getMockBuilder(EncoderFactoryInterface::class)->getMock();
        $encoderFactory
            ->expects($this->any())
            ->method('getEncoder')
            ->willReturn($passwordEncoder)
        ;

        return new DaoAuthenticationProvider($principalProvider, 'key', $encoderFactory);
    }
}
