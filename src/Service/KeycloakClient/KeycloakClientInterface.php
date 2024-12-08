<?php

namespace App\Service\KeycloakClient;

use App\Service\KeycloakClient\Dto\KeycloakUser;
use App\Service\KeycloakClient\Dto\RealmAuthDto;
use App\Service\KeycloakClient\Dto\RealmAuthInfo;
use App\Service\KeycloakClient\Dto\SearchUserDto;
use App\Service\KeycloakClient\Dto\UpdateUserPasswordDto;
use App\Service\KeycloakClient\Dto\ValidatePasswordDto;

interface KeycloakClientInterface
{
    public function authUser(RealmAuthDto $dto): ?RealmAuthInfo;
    public function getGroups(): array;
    public function getRoles(): array;
    public function getUsers(SearchUserDto $dto): array;
    public function getUser(string $uuid): KeycloakUser;
    public function getUserCredentials(string $uuid): array;

    public function resetUserPassword(UpdateUserPasswordDto $dto): void;

    public function validatePassword(ValidatePasswordDto $dto): bool;

    public function refreshToken(string $refreshToken): array;
}