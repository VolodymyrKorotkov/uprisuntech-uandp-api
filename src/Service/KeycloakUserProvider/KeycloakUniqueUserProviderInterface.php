<?php

namespace App\Service\KeycloakUserProvider;

use App\Service\KeycloakClient\Dto\KeycloakUser;

interface KeycloakUniqueUserProviderInterface
{
    /**
     * @throws KeycloakUserNotFoundException
     */
    public function getByEmail(string $email): KeycloakUser;
    public function getByUsername(string $username): KeycloakUser;
    public function getByIdentity(string $identity): KeycloakUser;
}
