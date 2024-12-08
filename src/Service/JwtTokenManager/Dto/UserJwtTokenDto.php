<?php declare(strict_types=1);

namespace App\Service\JwtTokenManager\Dto;

use Symfony\Component\Serializer\Annotation\SerializedName;

final readonly class UserJwtTokenDto
{
    public function __construct(
        public string $token,
        #[SerializedName('refresh_token')]
        public string $refreshToken
    )
    {
    }
}