<?php

namespace App\Service\OauthUserProvider;

use App\Service\OauthUserProvider\Dto\GetOauthUserDto;
use App\Service\OauthUserProvider\Dto\GetOauthUserResult;

interface OauthUserProviderInterface
{
    public function getOauthUser(GetOauthUserDto $dto): GetOauthUserResult;
}
