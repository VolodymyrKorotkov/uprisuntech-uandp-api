<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Resource\JwtCreateTokenByCredentials;
use App\Entity\Resource\JwtToken;
use App\Service\KeycloakAuth\AppUserKeycloakAuthInterface;
use App\Service\KeycloakClient\Dto\RealmAuthDto;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

final class ApiLoginPasswordAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        #[Autowire(service: 'lexik_jwt_authentication.handler.authentication_success')]
        private readonly AuthenticationSuccessHandlerInterface $successHandler,
        #[Autowire(service: 'lexik_jwt_authentication.handler.authentication_failure')]
        private readonly AuthenticationFailureHandlerInterface $failureHandler,
        private readonly AppUserKeycloakAuthInterface          $keycloakAuth,
        private readonly SerializerInterface                   $serializer
    )
    {
    }

    /**
     * @throws Throwable
     */
    final public function authenticate(Request $request): Passport
    {
        $authInfo = $this->getAuthCredentials($request);
        if ($authInfo->password === null || $authInfo->email === null) {
            throw $this->createInvalidUserCredentialsException();
        }
        try {
            $realmAuthDto = new RealmAuthDto();
            $realmAuthDto->username = $authInfo->email;
            $realmAuthDto->password = $authInfo->password;

            $keycloakAuthInfo = $this->keycloakAuth->authUser($realmAuthDto);
        } catch (ClientException $t) {
            throw $this->createInvalidUserCredentialsException();
        }

        if ($keycloakAuthInfo->userIdentity === null) {
            throw $this->createInvalidUserCredentialsException();
        }

        return new SelfValidatingPassport(
            new UserBadge($keycloakAuthInfo->userIdentity)
        );
    }

    final protected function createInvalidUserCredentialsException(): Throwable
    {
        return new CustomUserMessageAuthenticationException('Invalid user credentials.');
    }

    public function supports(Request $request): ?bool
    {
        return
            $request->attributes->get('_route') === JwtToken::LOGIN_BY_CREDENTIALS &&
            $request->isMethod('POST');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return $this->successHandler->onAuthenticationSuccess($request, $token);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return $this->failureHandler->onAuthenticationFailure($request, $exception);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    protected function getAuthCredentials(Request $request): JwtCreateTokenByCredentials
    {
        return $this->serializer->denormalize($request->toArray(), JwtCreateTokenByCredentials::class);
    }
}
