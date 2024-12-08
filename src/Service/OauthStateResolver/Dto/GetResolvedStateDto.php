<?php declare(strict_types=1);

namespace App\Service\OauthStateResolver\Dto;

final readonly class GetResolvedStateDto
{
    public function __construct(
        public string $redirectUrl,
        public ?string $userState
    )
    {
    }
}
