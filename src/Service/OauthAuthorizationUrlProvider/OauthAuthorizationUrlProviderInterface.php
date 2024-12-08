<?php

namespace App\Service\OauthAuthorizationUrlProvider;

use App\Service\OauthAuthorizationUrlProvider\Dto\GetAuthorizationUrlDto;

interface OauthAuthorizationUrlProviderInterface
{
    public function getAuthorizationUrl(GetAuthorizationUrlDto $dto): string;
}
