<?php

namespace Symfony\Component\Security\Principal\Tests\Authenticator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Principal\Authentication\Authenticator\HttpBasicAuthenticator;
use Symfony\Component\Security\Principal\Exception\UnsupportedPrincipalException;
use Symfony\Component\Security\Principal\Principal\PasswordPrincipalInterface;
use Symfony\Component\Security\Principal\Principal\PrincipalInterface;
use Symfony\Component\Security\Principal\Principal\PrincipalProviderInterface;

class HttpBasicAuthenticatorTest extends TestCase
{
    /** @var PrincipalProviderInterface|MockObject */
    private $principalProvider;
    /** @var EncoderFactoryInterface|MockObject */
    private $encoderFactory;
    /** @var PasswordEncoderInterface|MockObject */
    private $encoder;

    protected function setUp()
    {
        $this->principalProvider = $this->getMockBuilder(PrincipalProviderInterface::class)->getMock();
        $this->encoderFactory = $this->getMockBuilder(EncoderFactoryInterface::class)->getMock();
        $this->encoder = $this->getMockBuilder(PasswordEncoderInterface::class)->getMock();
        $this->encoderFactory
            ->expects($this->any())
            ->method('getEncoder')
            ->willReturn($this->encoder);
    }

    public function testValidUsernameAndPasswordServerParameters()
    {
        $request = new Request([], [], [], [], [], [
            'PHP_AUTH_USER' => 'TheUsername',
            'PHP_AUTH_PW' => 'ThePassword',
        ]);

        $guard = new HttpBasicAuthenticator('test', $this->principalProvider, $this->encoderFactory);
        $credentials = $guard->getCredentials($request);
        $this->assertEquals([
            'username' => 'TheUsername',
            'password' => 'ThePassword',
        ], $credentials);

        $mockedPrincipal = $this->getMockBuilder([PrincipalInterface::class, PasswordPrincipalInterface::class])->getMock();
        $mockedPrincipal->expects($this->any())->method('getPassword')->willReturn('ThePassword');

        $this->principalProvider
            ->expects($this->any())
            ->method('loadByIdentifier')
            ->with('TheUsername')
            ->willReturn($mockedPrincipal);

        $principal = $guard->getPrincipal($credentials);
        $this->assertSame($mockedPrincipal, $principal);

        $this->encoder
            ->expects($this->any())
            ->method('isPasswordValid')
            ->with('ThePassword', 'ThePassword', null)
            ->willReturn(true);

        $checkCredentials = $guard->checkCredentials($credentials, $principal);
        $this->assertEquals(true, $checkCredentials);
    }

    /** @dataProvider provideInvalidPasswords */
    public function testInvalidPassword($presentedPassword, $exceptionMessage)
    {
        $guard = new HttpBasicAuthenticator('test', $this->principalProvider, $this->encoderFactory);

        $this->encoder
            ->expects($this->any())
            ->method('isPasswordValid')
            ->willReturn(false);

        $this->expectException(BadCredentialsException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $guard->checkCredentials([
            'username' => 'TheUsername',
            'password' => $presentedPassword,
        ], $this->getMockBuilder([PrincipalInterface::class, PasswordPrincipalInterface::class])->getMock());
    }

    public function provideInvalidPasswords()
    {
        return [
            ['InvalidPassword', 'The presented password is invalid.'],
            ['', 'The presented password cannot be empty.'],
        ];
    }

    public function testNoPasswordPrincipal()
    {
        $guard = new HttpBasicAuthenticator('test', $this->principalProvider, $this->encoderFactory);

        $this->encoder
            ->expects($this->any())
            ->method('isPasswordValid')
            ->willReturn(false);

        $this->expectException(UnsupportedPrincipalException::class);
        $this->expectExceptionMessage('Principal does not implement PasswordPrincipalInterface.');

        $guard->checkCredentials([
            'username' => 'TheUsername',
            'password' => 'ThePassword',
        ], $this->getMockBuilder(PrincipalInterface::class)->getMock());
    }

    /** @dataProvider provideMissingHttpBasicServerParameters */
    public function testHttpBasicServerParametersMissing(array $serverParameters)
    {
        $request = new Request([], [], [], [], [], $serverParameters);

        $guard = new HttpBasicAuthenticator('test', $this->principalProvider, $this->encoderFactory);
        $this->assertFalse($guard->supports($request));
    }

    public function provideMissingHttpBasicServerParameters()
    {
        return [
            [[]],
            [['PHP_AUTH_PW' => 'ThePassword']],
        ];
    }
}
