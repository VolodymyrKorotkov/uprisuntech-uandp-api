<?php

namespace App\Service\OauthKeycloakProvider;

use GuzzleHttp\Client as HttpClient;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

class KeycloakProvider extends AbstractProvider
{
    public function __construct(
        private readonly KeycloakConfig          $config,
        private readonly KeycloakUrlBuilder      $urlBuilder,
        private readonly KeycloakResponseHandler $responseHandler
    )
    {
        $options = $this->getOptions($config);

        parent::__construct($options, $this->getCollaborators($options));
    }

    public function getBaseAuthorizationUrl(): string
    {
        return $this->urlBuilder->buildAuthorizationUrl($this->config);
    }

    public function getBaseAccessTokenUrl(array $params): string
    {
        return $this->urlBuilder->buildAccessTokenUrl($this->config);
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        $t = $token->getToken();
        $url = $this->urlBuilder->buildResourceOwnerDetailsUrl($this->config);

        return $url . "?access_token=$t";
    }

    /**
     * @param KeycloakConfig $config
     * @return array
     */
    private function getOptions(KeycloakConfig $config): array
    {
        return [
            'clientId' => $config->clientId,
            'clientSecret' => $config->clientSecret,
            'redirectUri' => $config->redirectUri,
        ];
    }

    /**
     * @param array $options
     * @return array
     */
    public function getCollaborators(array $options): array
    {
        $httpOptions = array_intersect_key(
            $options,
            array_flip($this->getAllowedClientOptions($options))
        );
        $httpOptions['verify'] = false;

        return [
            'httpClient' => new HttpClient($httpOptions)
        ];
    }

    protected function createResourceOwner(array $response, AccessToken $token): KeycloakResourceOwner
    {
        return new KeycloakResourceOwner($response);
    }

    protected function getDefaultScopes(): array
    {
        return $this->config->getDefaultScopes();
    }

    protected function getScopeSeparator(): string
    {
        return ' ';
    }

    protected function checkResponse(ResponseInterface $response, $data): void
    {
        $this->responseHandler->checkResponse($response, $data);
    }

    protected function parseResponse(ResponseInterface $response): array|string
    {
        return $this->responseHandler->parseResponse($response);
    }

    protected function getAuthorizationHeaders($token = null): array
    {
        if ($token) {
            return [
                'Authorization' => 'Bearer ' . $token->getToken(),
            ];
        }
        return [];
    }
}
