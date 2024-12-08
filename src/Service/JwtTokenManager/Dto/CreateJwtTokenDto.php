<?php declare(strict_types=1);

namespace App\Service\JwtTokenManager\Dto;

final readonly class CreateJwtTokenDto
{
    public function __construct(
        public string $code
    )
    {
    }
}
