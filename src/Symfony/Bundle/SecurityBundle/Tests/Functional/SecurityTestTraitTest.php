<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\SecurityBundle\Tests\Functional;

use Symfony\Bundle\SecurityBundle\Test\SecurityTestTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\User\User;

class SecurityTestTraitTest extends AbstractWebTestCase
{
    use SecurityTestTrait;

    public function testLoginBeforeRequest()
    {
        $client = $this->createClient(['test_case' => 'StandardFormLogin']);

        $this->login('johannes', 'default');

        $text = $client->request('GET', '/profile')->text();
        $this->assertStringContainsString('Hello johannes!', $text);
        $this->assertStringContainsString('You\'re browsing to path "/profile".', $text);
    }

    public function testRemainLoggedIn()
    {
        $client = $this->createClient(['test_case' => 'StandardFormLogin']);

        $this->login('johannes', 'default');

        $text = $client->request('GET', '/profile')->text();
        $this->assertStringContainsString('Hello johannes!', $text, 'Not authenticated in the first request.');
        $this->assertStringContainsString('You\'re browsing to path "/profile".', $text);

        $text = $client->request('GET', '/profile')->text();
        $this->assertStringContainsString('Hello johannes!', $text, 'Not authenticated in the second request.');
        $this->assertStringContainsString('You\'re browsing to path "/profile".', $text);
    }

    public function testLoginInBetweenRequests()
    {
        $client = $this->createClient(['test_case' => 'StandardFormLogin']);

        $client->request('GET', '/profile');
        $response = $client->getResponse();
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('http://localhost/login', $response->getTargetUrl());

        $this->login('johannes', 'default');

        $text = $client->request('GET', '/profile')->text();
        $this->assertStringContainsString('Hello johannes!', $text, 'Not authenticated.');
        $this->assertStringContainsString('You\'re browsing to path "/profile".', $text);
    }

    public function testRequiresKernelToBeBooted()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Calling login() before createClient() or bootKernel() is not invalid.');

        $this->login('johannes', 'default');
    }
}
