<?php declare(strict_types=1);

namespace App\Service\OauthUserProvider\ProvideStrategy;

use App\Entity\User;
use App\Enum\OauthTypeEnum;
use App\Repository\UserRepository;
use App\Service\OauthKeycloakProvider\KeycloakResourceOwnerProviderInterface;
use App\Service\OauthUserProvider\Dto\GetOauthUserDto;
use App\Service\OauthUserProvider\Dto\GetOauthUserResult;

final readonly class KeycloakUserProviderStrategy implements OauthUserProvideStrategyInterface
{
    public function __construct(
        private KeycloakResourceOwnerProviderInterface $resourceOwnerProvider,
        private UserRepository $userRepository
    ){}

    public function handleOauthUser(GetOauthUserDto $dto): GetOauthUserResult
    {
        $resourceOwner = $this->resourceOwnerProvider->getResourceOwner($dto->code);
        $user = $this->userRepository->findByUserIdentity($resourceOwner->getId());

        if ($user){
            $isNew = false;
        } else {
            $user = new User();
            $isNew = true;
        }

        $user->fillFromKeycloakData($resourceOwner);
        $this->userRepository->save($user);

        return new GetOauthUserResult(isNewUser: $isNew, user: $user);
    }

    public function support(GetOauthUserDto $dto): bool
    {
        return $dto->oauthType === OauthTypeEnum::KEYCLOAK;
    }
}
