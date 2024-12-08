<?php

namespace App\Service\OauthKeycloakProvider;

class KeycloakUserDataExtractor
{
    private array $response;

    public function __construct(array $response)
    {
        $this->response = $response;
    }

    public function getId(): ?string
    {
        return $this->response['sub'] ?? null;
    }

    public function getEmail(): ?string
    {
        return $this->response['email'] ?? null;
    }

    public function getName(): ?string
    {
        return $this->response['name'] ?? null;
    }

    public function getUsername(): ?string
    {
        return $this->response['preferred_username'] ?? null;
    }

    public function getFirstName(): ?string
    {
        return $this->response['given_name'] ?? null;
    }

    public function getLastName(): ?string
    {
        return $this->response['family_name'] ?? null;
    }

    public function toArray(): array
    {
        return $this->response;
    }
}