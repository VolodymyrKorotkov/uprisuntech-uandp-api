<?php

namespace App\Service\KeycloakUserProvider;

use App\Entity\Sub\ApplicationUserCollection;
use App\Enum\UserRoleEnum;
use App\Repository\UserRepository;
use App\Service\KeycloakClient\KeycloakAdminClientInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

final readonly class KeycloakApplicationUserProvider implements KeycloakApplicationUserProviderInterface
{
    public function __construct(
        private KeycloakAdminClientInterface $keycloakUserClient,
        private UserRepository               $userRepository
    )
    {
    }

    public function getUsersByRole(UserRoleEnum $role): ApplicationUserCollection
    {
        $identities = [];
        foreach ($this->keycloakUserClient->getUsersByRole($role) as $keycloakUser) {
            $identities[] = $keycloakUser->id;
        }

        return new ApplicationUserCollection(
            $this->userRepository->findByIdentities($identities)
        );
    }
}
