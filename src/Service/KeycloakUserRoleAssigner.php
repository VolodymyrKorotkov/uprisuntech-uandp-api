<?php declare(strict_types=1);

namespace App\Service;

use App\Service\KeycloakClient\KeycloakAdminClientInterface;

final readonly class KeycloakUserRoleAssigner
{
    public function __construct(
        private KeycloakAdminClientInterface  $keycloakUserCrudClient
    )
    {
    }

    public function assignRole(string $userIdentity, string $role): void
    {
        $userRole = $this->keycloakUserCrudClient->getRoleByName($role);
        unset($userRole['attributes']);

        //todo: need refactor. Bug: other roles removed after assign this role
        $this->keycloakUserCrudClient->updateUserRolesRealmMapping(
            $userIdentity,
            [$userRole]
        );
    }
}
