<?php

namespace App\Service\KeycloakClient\Dto;

final class RealmAuthInfo
{
    private string|null $accessToken;
    private int|null $expiresIn;
    private int|null $refreshExpiresIn;
    private string|null $refreshToken;
    private string|null $tokenType;
    private string|null $sessionState;
    private \DateTime|null $generatedAt;
    private \DateTime|null $expireAt;

    public function __construct(
        string|null $accessToken = null,
        int|null    $expiresIn = null,
        int|null    $refreshExpiresIn = null,
        string|null $refreshToken = null,
        string|null $tokenType = null,
        string|null $sessionState = null
    )
    {
        $this->accessToken = $accessToken;
        $this->expiresIn = $expiresIn;
        $this->refreshExpiresIn = $refreshExpiresIn;
        $this->refreshToken = $refreshToken;
        $this->tokenType = $tokenType;
        $this->sessionState = $sessionState;
        $this->generatedAt = new \DateTime();
        $this->expireAt = (new \DateTime())->modify('+' . $this->expiresIn . 'seconds');;
    }

    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function setExpiresIn(int $expiresIn): void
    {
        $this->expiresIn = $expiresIn;
    }

    public function setRefreshExpiresIn(int $refreshExpiresIn): void
    {
        $this->refreshExpiresIn = $refreshExpiresIn;
    }

    public function setRefreshToken(string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    public function setTokenType(string $tokenType): void
    {
        $this->tokenType = $tokenType;
    }

    public function setSessionState(string $sessionState): void
    {
        $this->sessionState = $sessionState;
    }

    public function setGeneratedAt(\DateTime $generatedAt): void
    {
        $this->generatedAt = $generatedAt;
    }

    public function setExpireAt(\DateTime $expireAt): void
    {
        $this->expireAt = $expireAt;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    public function getAuthToken(): string
    {
        return $this->getTokenType() . ' ' . $this->getAccessToken();
    }

    static public function fromArray($array): self
    {
        return new self(
            accessToken: $array['access_token'],
            expiresIn: $array['expires_in'],
            refreshExpiresIn: $array['refresh_expires_in'],
            refreshToken: $array['refresh_token'],
            tokenType: $array['token_type'],
            sessionState: $array['session_state']
        );
    }
}