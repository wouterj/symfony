<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Http\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\RememberMeSupportedInterface;
use Symfony\Component\Security\Http\Event\CredentialsValidEvent;
use Symfony\Component\Security\Http\Event\CredentialsVerificationFailedEvent;
use Symfony\Component\Security\Http\EventListener\RememberMeListener;
use Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface;

class RememberMeListenerTest extends TestCase
{
    private $rememberMeServices;
    private $listener;
    private $request;
    private $response;
    private $token;

    protected function setUp(): void
    {
        $this->rememberMeServices = $this->createMock(RememberMeServicesInterface::class);
        $this->listener = new RememberMeListener($this->rememberMeServices, 'main_firewall');
        $this->request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
        $this->response = $this->createMock(Response::class);
        $this->token = $this->createMock(TokenInterface::class);
    }

    public function testValidCredentialsInOtherFirewall()
    {
        $this->rememberMeServices->expects($this->never())->method('loginSuccess');

        $event = $this->createCredentialsValidEvent('other_firewall', $this->response);
        $this->listener->onValidCredentials($event);
    }

    public function testValidCredentialsWithoutSupportingAuthenticator()
    {
        $this->rememberMeServices->expects($this->never())->method('loginSuccess');

        $event = $this->createCredentialsValidEvent('main_firewall', $this->response, $this->createMock(AuthenticatorInterface::class));
        $this->listener->onValidCredentials($event);
    }

    public function testValidCredentialsWithoutSuccessResponse()
    {
        $this->rememberMeServices->expects($this->never())->method('loginSuccess');

        $event = $this->createCredentialsValidEvent('main_firewall', null);
        $this->listener->onValidCredentials($event);
    }

    public function testValidCredentials()
    {
        $this->rememberMeServices->expects($this->once())->method('loginSuccess')->with($this->request, $this->response, $this->token);

        $event = $this->createCredentialsValidEvent('main_firewall', $this->response);
        $this->listener->onValidCredentials($event);
    }

    public function testCredentialsInvalidInOtherFirewall()
    {
        $this->rememberMeServices->expects($this->never())->method('loginFail');

        $event = $this->createCredentialsVerificationFailedEvent('other_firewall');
        $this->listener->onCredentialsVerificationFailed($event);
    }

    public function testCredentialsInvalid()
    {
        $this->rememberMeServices->expects($this->once())->method('loginFail')->with($this->request, $this->isInstanceOf(AuthenticationException::class));

        $event = $this->createCredentialsVerificationFailedEvent('main_firewall');
        $this->listener->onCredentialsVerificationFailed($event);
    }

    private function createCredentialsValidEvent($providerKey, $response, $authenticator = null)
    {
        return new CredentialsValidEvent($authenticator ?? $this->createMock([AuthenticatorInterface::class, RememberMeSupportedInterface::class]), $this->token, $this->request, $response, $providerKey);
    }

    private function createCredentialsVerificationFailedEvent($providerKey)
    {
        return new CredentialsVerificationFailedEvent(new AuthenticationException(), $this->createMock(AuthenticatorInterface::class), $this->request, null, $providerKey);
    }
}
