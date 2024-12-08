<?php declare(strict_types=1);

namespace App\Service\KeycloakUserSync;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\KeycloakClient\Dto\CreateUserDto;
use App\Service\KeycloakClient\Dto\UpdateUserDto;
use App\Service\KeycloakClient\KeycloakAdminClientInterface;
use App\Service\KeycloakUserProvider\KeycloakUserNotFoundException;
use App\Service\KeycloakUserProvider\KeycloakUniqueUserProviderInterface;
use App\Service\KeycloakUserSync\Dto\SyncToKeycloakDto;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;

final readonly class KeycloakUserSynchronizer implements KeycloakUserSynchronizerInterface
{
    public function __construct(
        private KeycloakUniqueUserProviderInterface $keycloakUserProvider,
        private UserRepository                      $userRepository,
        private LoggerInterface                     $logger,
        private KeycloakAdminClientInterface        $keycloakUserCrudClient
    ){}

    /**
     * @throws ClientExceptionInterface
     */
    public function syncUserToKeycloak(SyncToKeycloakDto $dto): void
    {
        $user = $this->userRepository->findByUserIdentity($dto->identity);

        if (!$user){
            return;
        }

        $this->userRepository->save(
            $this->createOrUpdateUserOnKeycloak($user)
        );
    }

    /**
     * @param User $user
     * @return User
     * @throws ClientExceptionInterface
     */
    public function createOrUpdateUserOnKeycloak(User $user): User
    {
        try {
            if ($user->hasUserIdentifier()) {
                return $this->updateUserOnKeycloak($user);
            } else {
                return $this->createUserOnKeycloak($user);
            }
        } catch (ClientExceptionInterface $exception) {
            if ($exception->getCode() === 404) {
                $this->logger->error('User not found in Keycloak', ['exception' => $exception]);

                return $this->createUserOnKeycloak($user);
            } else {
                throw $exception;
            }
        }
    }

    /**
     * @param User $user
     * @return User
     */
    private function updateUserOnKeycloak(User $user): User
    {
        $this->keycloakUserCrudClient->updateUser(
            UpdateUserDto::newFromUser($user)
        );

        return $user;
    }

    /**
     * @param User $user
     * @return User
     */
    private function createUserOnKeycloak(User $user): User
    {
        $username = uniqid();
        if($this->existUserInKeycloakByUsername($user)){
            $user->setEmail(null);
        }

        $this->keycloakUserCrudClient->createUser(
            CreateUserDto::newFromUser($user, $username)
        );

        $keycloakUser = $this->keycloakUserProvider->getByUsername($username);
        $user->setUserIdentifier($keycloakUser->id);

        return $user;
    }

    private function existUserInKeycloakByUsername(User $user): bool
    {
        try {
            $this->keycloakUserProvider->getByEmail($user->getEmail());
            return true;
        } catch (KeycloakUserNotFoundException){
            return false;
        }
    }
}
