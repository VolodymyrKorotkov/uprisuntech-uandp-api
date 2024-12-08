<?php

namespace App\Service\KeycloakAuth;

use App\Service\KeycloakClient\Dto\RealmAuthDto;

interface AppUserKeycloakAuthInterface
{
    public function authUser(RealmAuthDto $dto): AuthUserResult;
}
