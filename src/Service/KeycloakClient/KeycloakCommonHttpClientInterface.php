<?php

namespace App\Service\KeycloakClient;

use App\Service\KeycloakClient\Dto\KeycloakRequest;
use Symfony\Contracts\HttpClient\ResponseInterface;

interface KeycloakCommonHttpClientInterface
{
    public const REALM_AUTH = '/realms/{realm}/protocol/openid-connect/token';
    public const REALM_LOGOUT = '/admin/realms/{realm}/users/{userId}/logout';
    public const GET_GROUPS = '/admin/realms/{realm}/groups';
    public const GET_ROLES = '/admin/realms/{realm}/roles';
    public const GET_USERS = '/admin/realms/{realm}/users';
    public const GET_USER = '/admin/realms/{realm}/users/{uuid}';
    public const GET_USER_GROUPS = '/admin/realms/{realm}/users/{uuid}/groups';
    public const GET_USER_ROLES = '/admin/realms/{realm}/users/{uuid}/role-mappings';
    public const UPDATE_USER_ROLES = '/admin/realms/{realm}/users/{uuid}/role-mappings/realm';
    public const GET_ROLE_BY_NAME = '/admin/realms/{realm}/roles/{roleName}';

    public const GET_USER_CREDENTIALS = '/admin/realms/{realm}/users/{uuid}/credentials';

    public const CREATE_USER = '/admin/realms/{realm}/users';

    public const UPDATE_USER = '/admin/realms/{realm}/users/{uuid}';
    public const UPDATE_USER_PASSWORD = '/admin/realms/{realm}/users/{uuid}/reset-password';
    public const DELETE_USER = '/admin/realms/{realm}/users/{uuid}';
    public const GET_USERS_BY_ROLE = '/admin/realms/{realm}/roles/{roleName}/users';

    public function request(KeycloakRequest $request): ResponseInterface;
}
