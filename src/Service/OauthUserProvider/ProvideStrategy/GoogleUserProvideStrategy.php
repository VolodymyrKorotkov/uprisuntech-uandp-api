<?php

namespace App\Service\OauthUserProvider\ProvideStrategy;

use App\Entity\SyncUserFromKeycloak;
use App\Entity\User;
use App\Enum\OauthTypeEnum;
use App\Repository\UserRepository;
use App\Service\KeycloakUserProvider\KeycloakUserNotFoundException;
use App\Service\KeycloakUserSync\KeycloakUserSyncInterface;
use App\Service\OauthGoogleProvider\GoogleResourceOwnerProviderInterface;
use App\Service\OauthUserProvider\Dto\GetOauthUserDto;
use App\Service\OauthUserProvider\Dto\GetOauthUserResult;
use Doctrine\ORM\EntityNotFoundException;
use Exception;
use Google\Service\Oauth2\Userinfo;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final readonly class GoogleUserProvideStrategy implements OauthUserProvideStrategyInterface
{
    public function __construct(
        private GoogleResourceOwnerProviderInterface $googleResourceOwnerProvider,
        private KeycloakUserSyncInterface            $keycloakUserSync,
        private UserRepository                       $userRepository
    )
    {
    }

    /**
     * @throws Exception
     */
    public function handleOauthUser(GetOauthUserDto $dto): GetOauthUserResult
    {
        $resourceOwner = $this->googleResourceOwnerProvider->getResourceOwner($dto->code);

        try {
            $userSync = new SyncUserFromKeycloak;
            $userSync->setEmail($resourceOwner->email);
            $userSync = $this->keycloakUserSync->syncUserFromKeycloakByEmail($userSync);

            return new GetOauthUserResult(isNewUser: false, user: $userSync->getUser());
        } catch (KeycloakUserNotFoundException) {
            return new GetOauthUserResult(isNewUser: true, user:  $this->createUser($resourceOwner));
        }
    }

    /**
     * @throws Exception
     */
    private function createUser(Userinfo $resourceOwnerDto): User
    {
        try {
            $user = $this->userRepository->getByEmail($resourceOwnerDto->email);
            throw new AccessDeniedException('User with email '.$user->getEmail().' exists on backoffice but not found on keycloak');
        } catch (EntityNotFoundException){
            $user = new User();
            $user->fillFromGoogleData($resourceOwnerDto);

            $this->userRepository->save($user);
        }

        return $user;
    }

    public function support(GetOauthUserDto $dto): bool
    {
        return $dto->oauthType === OauthTypeEnum::GOOGLE;
    }
}
