<?php

namespace App\Service\KeycloakUserProvider;

use App\Entity\Sub\ApplicationUserCollection;
use App\Enum\UserRoleEnum;

interface KeycloakApplicationUserProviderInterface
{
    public function getUsersByRole(UserRoleEnum $role): ApplicationUserCollection;
}