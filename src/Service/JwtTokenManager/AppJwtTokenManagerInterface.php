<?php

namespace App\Service\JwtTokenManager;

use App\Service\JwtTokenManager\Dto\CreateJwtTokenDto;
use App\Service\JwtTokenManager\Dto\RefreshJwtTokenDto;
use App\Service\JwtTokenManager\Dto\UserJwtTokenDto;
use JetBrains\PhpStorm\Deprecated;

#[Deprecated]
interface AppJwtTokenManagerInterface
{
    public function createJwtToken(CreateJwtTokenDto $dto): UserJwtTokenDto;
    public function refreshJwtToken(RefreshJwtTokenDto $dto): UserJwtTokenDto;
}
