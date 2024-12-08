<?php

namespace App\Service\KeycloakUserSync;

use App\Entity\User;
use App\Service\KeycloakUserSync\Dto\SyncToKeycloakDto;

interface KeycloakUserSynchronizerInterface
{
    public function syncUserToKeycloak(SyncToKeycloakDto $dto): void;
    public function createOrUpdateUserOnKeycloak(User $user): User;
}
