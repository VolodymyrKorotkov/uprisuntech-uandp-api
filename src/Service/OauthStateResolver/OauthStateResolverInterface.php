<?php

namespace App\Service\OauthStateResolver;

use App\Service\OauthAuthorizationUrlProvider\Dto\GetAuthorizationUrlDto;
use App\Service\OauthStateResolver\Dto\GetResolvedStateDto;

interface OauthStateResolverInterface
{
    public function createOauthState(GetAuthorizationUrlDto $dto): string;
    public function getResolvedState(string $state): GetResolvedStateDto;
}
