<?php declare(strict_types=1);

namespace App\Controller\ApiPlatform\Dto;

final readonly class FormioTokenDto
{
    public function __construct(
        public string $token
    )
    {
    }
}
