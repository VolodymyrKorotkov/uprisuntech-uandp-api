<?php
namespace App\Service\JwtTokenManager;

use App\Entity\RefreshToken;
use App\Entity\User;
use App\Enum\UserRoleEnum;
use App\Exception\CodeNotFoundException;
use App\Repository\UserRepository;
use App\Security\AppUser;
use App\Service\JwtTokenManager\Dto\CreateJwtTokenDto;
use App\Service\JwtTokenManager\Dto\RefreshJwtTokenDto;
use App\Service\JwtTokenManager\Dto\UserJwtTokenDto;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class AppJwtTokenManager implements AppJwtTokenManagerInterface
{
    private JWTTokenManagerInterface $jwtManager;
    private UserRepository $userRepository;
    private RefreshTokenManagerInterface $refreshTokenManager;

    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        UserRepository $userRepository,
        RefreshTokenManagerInterface $refreshTokenManager
    ) {
        $this->jwtManager = $jwtManager;
        $this->userRepository = $userRepository;
        $this->refreshTokenManager = $refreshTokenManager;
    }

    /**
     * @throws CodeNotFoundException
     */
    public function createJwtToken(CreateJwtTokenDto $dto): UserJwtTokenDto
    {
        $user = $this->userRepository->findByCode($dto->code);

        if (!$user) {
            throw new CodeNotFoundException();
        }

        $user->removeCode();
        $this->userRepository->save($user);

        $refreshToken = $this->refreshTokenManager->getLastFromUsername($user->getUserIdentifier());
        if (!$refreshToken) {
            $refreshToken = new RefreshToken();
            $refreshToken->setUsername($user->getUserIdentifier());
        }
        $this->saveRefreshToken($refreshToken);

        return $this->createJwtTokenFromUser($user, $refreshToken);
    }


    public function refreshJwtToken(RefreshJwtTokenDto $dto): UserJwtTokenDto
    {
        $refreshToken = $this->refreshTokenManager->get($dto->refreshToken);

        if (!$refreshToken) {
            throw new \DomainException('Refresh token is invalid');
        }

        $user = $this->userRepository->findByUserIdentity($refreshToken->getUsername());

        $this->saveRefreshToken($refreshToken);

        return $this->createJwtTokenFromUser($user, $refreshToken);
    }

    /**
     * @param User|null $user
     * @param RefreshTokenInterface|null $refreshToken
     * @return UserJwtTokenDto
     */
    public function createJwtTokenFromUser(?User $user, ?RefreshTokenInterface $refreshToken): UserJwtTokenDto
    {
        $jwt = $this->jwtManager->create(
            new AppUser($user->getUserIdentifier(), [UserRoleEnum::ROLE_USER])
        );

        return new UserJwtTokenDto(
            token: $jwt,
            refreshToken: $refreshToken
        );
    }

    /**
     * @param RefreshTokenInterface|null $refreshToken
     * @return void
     */
    public function saveRefreshToken(?RefreshTokenInterface $refreshToken): void
    {
        $refreshToken->setValid((new \DateTime())->add(new \DateInterval('PT3600S')));
        $refreshToken->setRefreshToken();
        $this->refreshTokenManager->save($refreshToken);
    }
}
