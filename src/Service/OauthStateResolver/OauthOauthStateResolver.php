<?php declare(strict_types=1);

namespace App\Service\OauthStateResolver;

use App\Entity\OauthState;
use App\Repository\OauthStateRepository;
use App\Repository\SiteRedirectRepository;
use App\Service\OauthAuthorizationUrlProvider\Dto\GetAuthorizationUrlDto;
use App\Service\OauthStateResolver\Dto\GetResolvedStateDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class OauthOauthStateResolver implements OauthStateResolverInterface
{
    public function __construct(
        #[Autowire(env: 'OAUTH_APP_REDIRECT_URL')] private string $redirectUrl,
        private readonly SiteRedirectRepository                   $siteRedirect,
        private readonly OauthStateRepository                     $oauthStateRepository,
        private readonly EntityManagerInterface                   $em
    )
    {
    }

    public function createOauthState(GetAuthorizationUrlDto $dto): string
    {
        if ($dto->siteAlias) {
            $siteRedirect = $this->siteRedirect->findOneBy([
                'alias' => $dto->siteAlias
            ]);
        } else {
            $siteRedirect = null;
        }

        $oauthState = (new OauthState())->setUserState($dto->state)->setSiteRedirect($siteRedirect);
        $this->em->persist($oauthState);
        $this->em->flush();

        return $oauthState->getState();
    }

    public function getResolvedState(string $state): GetResolvedStateDto
    {
        $oauthState = $this->oauthStateRepository->findOneBy(['state' => $state]);

        if (!$oauthState){
            throw new NotFoundHttpException('State not found');
        }

        return new GetResolvedStateDto(
            redirectUrl: $oauthState->getSiteRedirect()?->getRedirectUrl() ?? $this->redirectUrl,
            userState: $oauthState->getUserState()
        );
    }
}
