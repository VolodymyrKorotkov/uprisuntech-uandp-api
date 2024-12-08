<?php declare(strict_types=1);

namespace App\Service\OauthGovIdProvider\Dto;

final readonly class GetGovIdResourceOwnerDto
{
    public function __construct(
        public string $code
    ) {}
}