<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\KeycloakUserProvider\KeycloakUniqueUserProviderInterface;
use Doctrine\ORM\EntityNotFoundException;

final readonly class AppUserProvider
{
    public function __construct(
        private UserRepository $applicationUserRepository,
        private KeycloakUniqueUserProviderInterface $keycloakUniqueUserProvider
    )
    {}

    public function getUser(string $userIdentity): User
    {
        try {
            $user = $this->applicationUserRepository->getByUserIdentity($userIdentity);
        } catch (EntityNotFoundException $e) {
            $user = new User();
            $user->setUserIdentifier($userIdentity);
            $user->rewriteFromKeycloakUser(
                $this->keycloakUniqueUserProvider->getByIdentity($userIdentity)
            );

            $this->applicationUserRepository->save($user);
        }

        return $user;
    }
}
