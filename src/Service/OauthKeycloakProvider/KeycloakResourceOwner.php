<?php

namespace App\Service\OauthKeycloakProvider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class KeycloakResourceOwner implements ResourceOwnerInterface
{
    private KeycloakUserDataExtractor $dataExtractor;
    private string $jwt;
    private string $refreshToken;

    public function __construct(array $response)
    {
        $this->dataExtractor = new KeycloakUserDataExtractor($response);
    }

    public function getId()
    {
        return $this->dataExtractor->getId();
    }

    public function getEmail()
    {
        return $this->dataExtractor->getEmail();
    }

    public function getName()
    {
        return $this->dataExtractor->getName();
    }

    public function getUsername()
    {
        return $this->dataExtractor->getUsername();
    }

    public function getFirstName()
    {
        return $this->dataExtractor->getFirstName();
    }

    public function getLastName()
    {
        return $this->dataExtractor->getLastName();
    }

    public function toArray()
    {
        return $this->dataExtractor->toArray();
    }

    public function withJwt(string $jwt, string $refreshToken): self
    {
        $res = clone $this;
        $res->jwt = $jwt;
        $res->refreshToken = $refreshToken;

        return $res;
    }

    public function getJwt(): string
    {
        return $this->jwt;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }
}
