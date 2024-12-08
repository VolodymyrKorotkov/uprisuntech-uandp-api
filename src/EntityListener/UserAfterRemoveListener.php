<?php declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\User;
use App\Service\KeycloakClient\Dto\DeleteUserDto;
use App\Service\KeycloakClient\KeycloakAdminClientInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;

#[AsEntityListener(event: Events::postRemove, method: 'postRemove', entity: User::class)]
final readonly class UserAfterRemoveListener
{
    public function __construct(
        private RefreshTokenManagerInterface $refreshTokenManager,
        private KeycloakAdminClientInterface $keycloakClient
    )
    {
    }

    public function postRemove(User $user): void
    {
        $refreshToken = $this->refreshTokenManager->getLastFromUsername($user->getUserIdentifier());
        if ($refreshToken) {
            $this->refreshTokenManager->delete($refreshToken);
        }

        $this->keycloakClient->deleteUser(
            DeleteUserDto::newFromUser($user)
        );
    }
}
