<?php

namespace App\Service\KeycloakUserSync;

use App\Entity\SyncUserFromKeycloak;
use App\Service\KeycloakUserProvider\KeycloakUserNotFoundException;

interface KeycloakUserSyncInterface
{
    /**
     * @throws KeycloakUserNotFoundException
     */
    public function syncUserFromKeycloakByEmail(SyncUserFromKeycloak $syncEntity): SyncUserFromKeycloak;
}
