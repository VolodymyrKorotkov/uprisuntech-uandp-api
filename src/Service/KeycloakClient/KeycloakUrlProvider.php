<?php

namespace App\Service\KeycloakClient;

use App\Service\KeycloakClient\Dto\SearchUserDto;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class KeycloakUrlProvider
{
    const REALM_AUTH = '/realms/{realm}/protocol/openid-connect/token';
    const REALM_LOGOUT = '/admin/realms/{realm}/users/{userId}/logout';
    const GET_GROUPS = '/admin/realms/{realm}/groups';
    const GET_ROLES = '/admin/realms/{realm}/roles';

    const GET_USERS = '/admin/realms/{realm}/users';
    const GET_USER = '/admin/realms/{realm}/users/{uuid}';
    const GET_USER_GROUPS = '/admin/realms/{realm}/users/{uuid}/groups';
    const GET_USER_ROLES = '/admin/realms/{realm}/users/{uuid}/role-mappings/realm/composite';
    const GET_USER_CREDENTIALS = '/admin/realms/{realm}/users/{uuid}/credentials';

    const CREATE_USER = '/admin/realms/{realm}/users';

    const UPDATE_USER = '/admin/realms/{realm}/users/{uuid}';
    const UPDATE_USER_PASSWORD = '/admin/realms/{realm}/users/{uuid}/reset-password';
    const DELETE_USER = '/admin/realms/{realm}/users/{uuid}';

    public function __construct(
        #[Autowire('%env(KEYCLOAK_REALM)%')]
        private string $realm,
        #[Autowire('%env(KEYCLOAK_API_URL)%')]
        private string $keycloakUrl,
    )
    {}

    public function getRealmAuthUrl(): string
    {
        return $this->keycloakUrl . str_replace('{realm}', $this->realm, self::REALM_AUTH);
    }

    public function getLogoutUrl(string $userId): string
    {
        return $this->keycloakUrl . str_replace(['{realm}', '{userId}'], [$this->realm, $userId], self::REALM_LOGOUT);
    }

    public function getGroupsUrl(): string
    {
        return $this->keycloakUrl . str_replace('{realm}', $this->realm, self::GET_GROUPS);
    }

    public function getRolesUrl(): string
    {
        return $this->keycloakUrl . str_replace('{realm}', $this->realm, self::GET_ROLES);
    }

    public function getUsersUrl(SearchUserDto $dto): string
    {
        return $this->keycloakUrl . str_replace('{realm}', $this->realm, self::GET_USERS) . '?' . $dto->toQuery();
    }

    public function getUserUrl(string $uuid): string
    {
        return $this->keycloakUrl . str_replace(['{realm}', '{uuid}'], [$this->realm, $uuid], self::GET_USER);
    }

    public function getUserGroupsUrl(string $uuid): string
    {
        return $this->keycloakUrl . str_replace(['{realm}', '{uuid}'], [$this->realm, $uuid], self::GET_USER_GROUPS);
    }

    public function getUserRolesUrl(string $uuid): string
    {
        return $this->keycloakUrl . str_replace(['{realm}', '{uuid}'], [$this->realm, $uuid], self::GET_USER_ROLES);
    }

    public function getUserCredentialsUrl(string $uuid): string
    {
        return $this->keycloakUrl . str_replace(['{realm}', '{uuid}'], [$this->realm, $uuid], self::GET_USER_CREDENTIALS);
    }

    public function createUserUrl(): string
    {
        return $this->keycloakUrl . str_replace('{realm}', $this->realm, self::CREATE_USER);
    }

    public function updateUserUrl(string $uuid): string
    {
        return $this->keycloakUrl . str_replace(['{realm}', '{uuid}'], [$this->realm, $uuid], self::UPDATE_USER);
    }

    public function resetUserPasswordUrl(string $uuid): string
    {
        return $this->keycloakUrl . str_replace(['{realm}', '{uuid}'], [$this->realm, $uuid], self::UPDATE_USER_PASSWORD);
    }

    public function getUserDeleteUrl(Dto\DeleteUserDto $dto): string
    {
        return $this->keycloakUrl . str_replace(['{realm}', '{uuid}'], [$this->realm, $dto->uuid], self::DELETE_USER);
    }
}
