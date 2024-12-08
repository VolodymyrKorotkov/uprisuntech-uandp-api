<?php declare(strict_types=1);

namespace App\Security;

use App\Entity\Resource\JwtToken;
use App\Repository\UserRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

final class OauthCodeJwtAuthenticator extends AbstractOauthCodeAuthenticator
{
    public function __construct(
        private readonly UserRepository $userRepository,
        #[Autowire(service: 'lexik_jwt_authentication.handler.authentication_success')]
        private readonly AuthenticationSuccessHandlerInterface $successHandler,
        #[Autowire(service: 'lexik_jwt_authentication.handler.authentication_failure')]
        private readonly AuthenticationFailureHandlerInterface $failureHandler
    )
    {
        parent::__construct($this->userRepository);
    }

    public function supports(Request $request): ?bool
    {
        return
            $request->attributes->get('_route') === JwtToken::CREATE_BY_CODE_ROUTE_NAME &&
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
    protected function getCode(Request $request): mixed
    {
        return $request->toArray()['code'] ?? null;
    }
}
