<?php

namespace App\Service\OauthGoogleProvider;

use Google\Client;

interface GoogleClientFactoryInterface
{
    public function createClient(): Client;
}