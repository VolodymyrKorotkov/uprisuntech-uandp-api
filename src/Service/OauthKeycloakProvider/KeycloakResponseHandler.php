<?php

namespace App\Service\OauthKeycloakProvider;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Psr\Http\Message\ResponseInterface;

class KeycloakResponseHandler
{
    public function checkResponse(ResponseInterface $response, array|string $data)
    {
        $parsedData = $this->parseData($data);

        $this->handleErrorResponse($response, $parsedData);
    }

    public function parseResponse(ResponseInterface $response): array
    {
        $content = (string) $response->getBody();

        return $this->decodeJson($content);
    }

    private function parseData(array|string $data): array
    {
        if (is_string($data)) {
            return $this->decodeJson($data);
        }

        return $data;
    }

    private function decodeJson(string $json): array
    {
        $decoded = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \UnexpectedValueException("Error parsing JSON: " . json_last_error_msg());
        }

        return $decoded;
    }

    private function handleErrorResponse(ResponseInterface $response, array $data): void
    {
        if (!empty($data['error'])) {
            $this->throwIdentityProviderException($response, $data);
        }
    }

    private function throwIdentityProviderException(ResponseInterface $response, array $data): void
    {
        $error = $data['error'] . (isset($data['error_description']) ? ': ' . $data['error_description'] : '');
        throw new IdentityProviderException($error, $response->getStatusCode(), $data);
    }
}