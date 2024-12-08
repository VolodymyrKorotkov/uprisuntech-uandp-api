<?php

namespace App\Service\JwtTokenManager\Dto;

final readonly class RefreshJwtTokenDto
{
    public ?string $refreshToken;

    public function __construct(?string $refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }
}
