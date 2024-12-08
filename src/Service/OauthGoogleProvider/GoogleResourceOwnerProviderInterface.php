<?php

namespace App\Service\OauthGoogleProvider;

use Google\Service\Oauth2\Userinfo;

interface GoogleResourceOwnerProviderInterface
{
    public function getResourceOwner(string $code): Userinfo;
}