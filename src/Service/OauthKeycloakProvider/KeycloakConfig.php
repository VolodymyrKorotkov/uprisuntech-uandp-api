<?php

namespace App\Service\OauthKeycloakProvider;

use App\Enum\OauthTypeEnum;
use App\Service\OauthCallbackUrlGenerator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class KeycloakConfig
{
    public string $redirectUri;

    public function __construct(
        #[Autowire(env: 'KEYCLOAK_URL')]
        public string                     $authUrl,
        #[Autowire(env: 'KEYCLOAK_API_URL')]
        public string                     $apiUrl,
        #[Autowire(env: 'KEYCLOAK_REALM')]
        public string                     $realm,
        #[Autowire(env: 'KEYCLOAK_CLIENT_ID')]
        public string                     $clientId,
        #[Autowire(env: 'KEYCLOAK_CLIENT_SECRET')]
        public string                     $clientSecret,
        private OauthCallbackUrlGenerator $oauthCallbackUrlGenerator
    )
    {
        $this->redirectUri = $this->oauthCallbackUrlGenerator->generateCallbackUrl(OauthTypeEnum::KEYCLOAK);
    }

    public function getDefaultScopes(): array
    {
        return ['openid', 'profile', 'email'];
    }
}
