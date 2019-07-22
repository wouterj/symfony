<?php

namespace Symfony\Component\Security\Principal\Authentication\Authenticator;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Principal\Authentication\AuthenticatorInterface;
use Symfony\Component\Security\Principal\Principal\PrincipalProviderInterface;

/**
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class HttpBasicAuthenticator implements AuthenticatorInterface
{
    use PrincipalProviderTrait, UsernamePasswordTrait;

    protected $realmName;
    /** @var PrincipalProviderInterface */
    private $principalProvider;
    /** @var LoggerInterface|null */
    private $logger;

    public function __construct(string $realmName, PrincipalProviderInterface $principalProvider, EncoderFactoryInterface $encoderFactory, ?LoggerInterface $logger = null)
    {
        $this->realmName = $realmName;
        $this->principalProvider = $principalProvider;
        $this->encoderFactory = $encoderFactory;
        $this->logger = $logger;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $response = new Response();
        $response->headers->set('WWW-Authenticate', sprintf('Basic realm="%s"', $this->realmName));
        $response->setStatusCode(401);

        return $response;
    }

    public function supports(Request $request): bool
    {
        return $request->headers->has('PHP_AUTH_USER');
    }

    public function getCredentials(Request $request)
    {
        return [
            'username' => $request->headers->get('PHP_AUTH_USER'),
            'password' => $request->headers->get('PHP_AUTH_PW', ''),
        ];
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if (null !== $this->logger) {
            $this->logger->info('Basic authentication failed for user.', ['username' => $request->headers->get('PHP_AUTH_USER'), 'exception' => $exception]);
        }

        return $this->start($request, $exception);
    }

    /** @todo not sure if true or false */
    public function supportsRememberMe(): bool
    {
        return true;
    }
}
