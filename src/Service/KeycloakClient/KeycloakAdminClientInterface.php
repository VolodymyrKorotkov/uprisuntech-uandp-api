<?php

namespace App\Service\KeycloakClient;

use App\Enum\UserRoleEnum;
use App\Service\KeycloakClient\Dto\CreateUserDto;
use App\Service\KeycloakClient\Dto\DeleteUserDto;
use App\Service\KeycloakClient\Dto\KeycloakUser;
use App\Service\KeycloakClient\Dto\KeycloakUserCollection;
use App\Service\KeycloakClient\Dto\SearchUserDto;
use App\Service\KeycloakClient\Dto\UpdateUserDto;

interface KeycloakAdminClientInterface
{
    public function getUsers(SearchUserDto $dto): KeycloakUserCollection;
    public function getUser(string $identity): KeycloakUser;
    public function getUsersByRole(UserRoleEnum $roleEnum): KeycloakUserCollection;
    public function deleteUser(DeleteUserDto $dto): void;
    public function updateUser(UpdateUserDto $dto): void;
    public function createUser(CreateUserDto $dto): void;
    public function getUserRolesRealmMapping(string $uuid): array;
    public function updateUserRolesRealmMapping(string $uuid, array $roles): void;
    public function getRoleByName(string $roleName): array;
}
