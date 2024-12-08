<?php

namespace App\Service\OauthKeycloakProvider;

interface KeycloakResourceOwnerProviderInterface
{
    public function getResourceOwner(string $code): KeycloakResourceOwner;
}
