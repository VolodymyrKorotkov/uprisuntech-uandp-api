<?php

namespace App\Service\OauthCallbackHandler;

use App\Entity\OauthState;
use App\Repository\OauthStateRepository;
use App\Repository\UserRepository;
use App\Service\OauthCallbackHandler\Dto\HandleOauthCallbackDto;
use App\Service\OauthCallbackHandler\Dto\HandleOauthCallbackResult;
use App\Service\OauthUserProvider\Dto\GetOauthUserDto;
use App\Service\OauthUserProvider\Dto\GetOauthUserResult;
use App\Service\OauthUserProvider\OauthUserProviderInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsAlias]
final readonly class OauthCallbackHandler implements OauthCallbackHandlerInterface
{
    public function __construct(
        private OauthUserProviderInterface  $oauthUserProvider,
        #[Autowire(env: 'OAUTH_APP_REDIRECT_URL')] private string $redirectUrl,
        private UserRepository              $userRepository,
        private OauthStateRepository $oauthStateRepository
    )
    {
    }

    /**
     * @throws \Exception
     */
    public function handleOauthCallback(HandleOauthCallbackDto $dto): HandleOauthCallbackResult
    {
        $resourceOwner = $this->oauthUserProvider->getOauthUser(
            new GetOauthUserDto(code: $dto->code, oauthType: $dto->oauthType)
        );

        $resourceOwner->user->generateCode();
        $this->userRepository->save($resourceOwner->user);

        return new HandleOauthCallbackResult(
            redirectUrl: $this->getRedirectUrl($dto, $resourceOwner)
        );
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    private function getRedirectUrl(HandleOauthCallbackDto $dto, GetOauthUserResult $resourceOwnerResult): string
    {
        $stateResolved = $this->oauthStateRepository->getByState($dto->state);

        $user = $this->userRepository->findByUserIdentity(
            $resourceOwnerResult->user->getUserIdentifier()
        );

        $options = [
            'code' => $user->getCode(),
            'state' => $stateResolved->getUserState(),
            'dia' => 1,
            'siteAlias' => $stateResolved->getSiteRedirect()?->getAlias()
        ];

        if ($resourceOwnerResult->isNewUser) {
            $options['uuid'] = $user->getUserIdentifier();
        }

        return $this->_getRedirectUrl($stateResolved) . '?' . http_build_query($options);
    }

    private function _getRedirectUrl(OauthState $stateResolved): string
    {
        return $stateResolved->getSiteRedirect()?->getRedirectUrl() ?? $this->redirectUrl;
    }
}
