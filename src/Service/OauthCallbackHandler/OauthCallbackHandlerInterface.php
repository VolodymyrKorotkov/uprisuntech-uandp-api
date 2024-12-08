<?php

namespace App\Service\OauthCallbackHandler;

use App\Service\OauthCallbackHandler\Dto\HandleOauthCallbackDto;
use App\Service\OauthCallbackHandler\Dto\HandleOauthCallbackResult;

interface OauthCallbackHandlerInterface
{
    public function handleOauthCallback(HandleOauthCallbackDto $dto): HandleOauthCallbackResult;
}
