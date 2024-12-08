<?php declare(strict_types=1);

namespace App\Security;

use App\Enum\UserRoleEnum;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class AppUser implements UserInterface, JWTUserInterface
{
    public function __construct(
        private string $userIdentifier,
        private array $roles
    )
    {
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = UserRoleEnum::ROLE_USER_CASE->value;

        return array_unique($roles);
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }

    public static function createFromPayload($username, array $payload): AppUser|JWTUserInterface
    {
        return new self(
            $username,
            $payload['realm_access']['roles'],
        );
    }

    public static function getPayload(string $username, array $roles): array
    {
        return [
            'sub' => $username,
            'realm_access' => [
                'roles' => $roles
            ]
        ];
    }
}
