<?php declare(strict_types=1);

namespace App\Service\KeycloakAuth;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\KeycloakClient\Dto\RealmAuthDto;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator(AppUserKeycloakAuthInterface::class)]
final readonly class SyncUserDecorator implements AppUserKeycloakAuthInterface
{
    public function __construct(
        private AppUserKeycloakAuthInterface $keycloakAuth,
        private UserRepository               $userRepository
    )
    {
    }

    public function authUser(RealmAuthDto $dto): AuthUserResult
    {
        $authUser = $this->keycloakAuth->authUser($dto);

        if ($this->userRepository->existsByUserIdentity($authUser->userIdentity)) {
            return $authUser;
        }

        $user = new User();

        $user->setUserIdentifier($authUser->userIdentity);
        $user->setName($authUser->givenName);
        $user->setLastname($authUser->familyName);
        $user->setEmail($authUser->preferredUsername);

        $this->userRepository->save($user);

        return $authUser;
    }
}
