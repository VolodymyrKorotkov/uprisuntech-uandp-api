<?php

namespace App\Service\OauthKeycloakProvider;

use App\Exception\InvalidOauthCodeException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;

final readonly class KeycloakResourceOwnerProvider implements KeycloakResourceOwnerProviderInterface
{
    public function __construct(
        private KeycloakProvider $provider
    )
    {
    }

    /**
     * @throws IdentityProviderException
     */
    private function getAccessToken(string $code): AccessToken
    {
        return $this->provider->getAccessToken('authorization_code', [
            'code' => $code
        ]);
    }

    public function getResourceOwner(string $code): KeycloakResourceOwner
    {
        try {
            $jwt = $this->getAccessToken($code);
            $resourceOwner = $this->provider->getResourceOwner($jwt);

            if (!$resourceOwner instanceof KeycloakResourceOwner) {
                throw new \RuntimeException('Resource owner is not an instance of KeycloakResourceOwner');
            }

            return $resourceOwner->withJwt($jwt->getToken(), $jwt->getRefreshToken());
        } catch (\Throwable $e) {
            throw new InvalidOauthCodeException('Failed to get resource owner: ' . $e->getMessage());
        }
    }
}
