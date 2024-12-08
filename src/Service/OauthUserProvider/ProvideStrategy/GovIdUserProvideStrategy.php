<?php declare(strict_types=1);

namespace App\Service\OauthUserProvider\ProvideStrategy;

use App\Entity\User;
use App\Enum\OauthTypeEnum;
use App\Repository\UserRepository;
use App\Service\OauthGovIdProvider\Dto\GetGovIdResourceOwnerDto;
use App\Service\OauthGovIdProvider\Dto\GovIdResourceOwnerDto;
use App\Service\OauthGovIdProvider\GovIdResourceOwnerProviderInterface;
use App\Service\OauthUserProvider\Dto\GetOauthUserDto;
use App\Service\OauthUserProvider\Dto\GetOauthUserResult;
use Exception;

final readonly class GovIdUserProvideStrategy implements OauthUserProvideStrategyInterface
{
    public function __construct(
        private UserRepository                      $userRepository,
        private GovIdResourceOwnerProviderInterface $resourceOwnerProvider,
    ){}

    /**
     * @throws Exception
     */
    public function handleOauthUser(GetOauthUserDto $dto): GetOauthUserResult
    {
        $resourceDto = $this->resourceOwnerProvider->getResourceOwner(
            new GetGovIdResourceOwnerDto(code: $dto->code)
        );

        $user =
            $this->userRepository->findByDrfoCode($resourceDto->drfocode) ??
            $this->userRepository->findByEdrpouCode($resourceDto->edrpoucode);
        $isNew = !$user;

        if ($isNew) {
            $user = $this->createUser($resourceDto);
        } else {
            $user->updateFromGovUaData($resourceDto);
        }

        $this->userRepository->save($user);

        return new GetOauthUserResult(isNewUser: $isNew, user: $user);
    }

    public function support(GetOauthUserDto $dto): bool
    {
        return $dto->oauthType === OauthTypeEnum::GOV_ID;
    }

    /**
     * @throws Exception
     */
    private function createUser(GovIdResourceOwnerDto $resourceOwnerDto): User
    {
        $user = new User();
        $user->fillFromGovUaData($resourceOwnerDto);

        $emailExists = empty($resourceOwnerDto->email) || !empty(
            $this->userRepository->findByEmail($resourceOwnerDto->email)
        );

        if ($emailExists){
            $user->setEmail(null);
        }

        return $user;
    }
}
