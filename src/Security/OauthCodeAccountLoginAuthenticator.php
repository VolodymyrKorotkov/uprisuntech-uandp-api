<?php declare(strict_types=1);

namespace App\Security;

use App\Entity\SiteRedirect;
use App\Enum\AppRouteNameEnum;
use App\Enum\OauthParameterEnum;
use App\Repository\SiteRedirectRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

//todo: rm after front fix uuid
#[AsAlias('app.user_service.security.authenticator.oauth_code_account_login')]
final class OauthCodeAccountLoginAuthenticator extends AbstractOauthCodeAuthenticator
{
    public function __construct(
        UserRepository                         $userRepository,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly SiteRedirectRepository $siteRedirectRepository
    )
    {
        parent::__construct($userRepository);
    }

    /**
     * @return RedirectResponse
     */
    private function getDefaultRedirectResponse(): RedirectResponse
    {
        return new RedirectResponse(
            $this->urlGenerator->generate(AppRouteNameEnum::ACCOUNT_INDEX)
        );
    }

    protected function getCode(Request $request): mixed
    {
        return $request->query->get('code');
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === AppRouteNameEnum::ACCOUNT_LOGIN
            && $request->query->has('code');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if (!$request->query->has(key: OauthParameterEnum::SITE_ALIAS->value)) {
            return $this->getDefaultRedirectResponse();
        }

        try {
            $siteRedirect = $this->siteRedirectRepository->getByAlias(
                $request->get(OauthParameterEnum::SITE_ALIAS->value)
            );
        } catch (EntityNotFoundException) {
            return $this->getDefaultRedirectResponse();
        }

        if (!$siteRedirect->hasAuthSuccessRedirectUrl()) {
            return $this->getDefaultRedirectResponse();
        }

        return new RedirectResponse(
            $this->getSuccessRedirectUrl($siteRedirect, $request)
        );
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new RedirectResponse(
            $this->urlGenerator->generate(AppRouteNameEnum::ACCOUNT_LOGIN)
        );
    }

    private function getSuccessRedirectUrl(SiteRedirect $siteRedirect, Request $request): string
    {
        return $siteRedirect->getAuthSuccessRedirectUrl().'?'.http_build_query($request->query->all());
    }
}
