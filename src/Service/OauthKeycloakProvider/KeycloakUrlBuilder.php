<?php

namespace App\Service\OauthKeycloakProvider;

class KeycloakUrlBuilder
{
    public function buildAuthorizationUrl(KeycloakConfig $config): string
    {
        return $config->authUrl . '/realms/' . $config->realm . '/protocol/openid-connect/auth';
    }

    public function buildAccessTokenUrl(KeycloakConfig $config): string
    {
        return $config->apiUrl . '/realms/' . $config->realm . '/protocol/openid-connect/token';
    }

    public function buildResourceOwnerDetailsUrl(KeycloakConfig $config): string
    {
        return $config->apiUrl . '/realms/' . $config->realm . '/protocol/openid-connect/userinfo';
    }
}
