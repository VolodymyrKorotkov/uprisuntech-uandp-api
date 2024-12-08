<?php declare(strict_types=1);

namespace App\Service\KeycloakUserSync;

use App\Entity\SyncUserFromKeycloak;
use App\Entity\User;
use App\Repository\SyncUserFromKeycloakRepository;
use App\Repository\UserRepository;
use App\Service\KeycloakUserProvider\KeycloakUserNotFoundException;
use App\Service\KeycloakUserProvider\KeycloakUniqueUserProviderInterface;
use Doctrine\ORM\EntityNotFoundException;

final readonly class KeycloakUserSync implements KeycloakUserSyncInterface
{
    public function __construct(
        private KeycloakUniqueUserProviderInterface $keycloakUserProvider,
        private UserRepository                      $userRepository,
        private SyncUserFromKeycloakRepository      $fromKeycloakRepository
    )
    {}

    /**
     * @throws KeycloakUserNotFoundException
     */
    public function syncUserFromKeycloakByEmail(SyncUserFromKeycloak $syncEntity): SyncUserFromKeycloak
    {
        try {
            $user = $this->userRepository->getByEmail($syncEntity->getEmail());
        } catch (EntityNotFoundException) {
            $user = new User();
        }

        $keycloakUser = $this->keycloakUserProvider->getByEmail($syncEntity->getEmail());
        $user->rewriteFromKeycloakUser($keycloakUser);

        $this->userRepository->save($user);

        $syncEntity->setUser($user);
        $this->fromKeycloakRepository->save($syncEntity);

        return $syncEntity;
    }
}
