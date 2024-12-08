<?php declare(strict_types=1);

namespace App\Controller;

use App\Enum\AppRouteNameEnum;
use App\Enum\OauthTypeEnum;
use App\Exception\InvalidOauthCodeException;
use App\Service\OauthAuthorizationUrlProvider\Dto\GetAuthorizationUrlDto;
use App\Service\OauthAuthorizationUrlProvider\OauthAuthorizationUrlProviderInterface;
use App\Service\OauthCallbackHandler\Dto\HandleOauthCallbackDto;
use App\Service\OauthCallbackHandler\OauthCallbackHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\EnumRequirement;

#[AsController]
final class OauthController extends AbstractController
{
    #[Route(
        path: 'oauth/{oauthType}/login',
        name: 'app.user_service.public.oauth_login',
        requirements: [
            'oauthType' => new EnumRequirement(OauthTypeEnum::class)
        ],
        methods: 'GET'
    )]
    public function login(
        OauthAuthorizationUrlProviderInterface $oauthProvider,
        #[MapQueryString] GetAuthorizationUrlDto $dto,
        string $oauthType
    ): Response
    {
        $dto->oauthType = OauthTypeEnum::tryFrom($oauthType);

        return $this->redirect($oauthProvider->getAuthorizationUrl($dto));
    }

    #[Route(
        path: 'oauth/{oauthType}/redirect',
        name: AppRouteNameEnum::OAUTH_CALL_BACK_URL_NAME,
        requirements: [
            'oauthType' => new EnumRequirement(OauthTypeEnum::class)
        ],
        methods: 'GET'
    )]
    public function callback(
        OauthCallbackHandlerInterface $callbackHandler,
        HandleOauthCallbackDto $dto
    ): RedirectResponse
    {
        try {
            return $this->redirect(
                $callbackHandler->handleOauthCallback($dto)->redirectUrl
            );
        } catch (InvalidOauthCodeException $exception){
            $this->addFlash('danger', $exception->getMessage());

            return $this->redirectToRoute(
                AppRouteNameEnum::ACCOUNT_LOGIN
            );
        }
    }
}
