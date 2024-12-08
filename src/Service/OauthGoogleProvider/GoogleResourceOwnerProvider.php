<?php

namespace App\Service\OauthGoogleProvider;

use Google\Service\Oauth2;
use Google\Service\Oauth2\Userinfo;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;

#[AsAlias(GoogleResourceOwnerProviderInterface::class)]
final readonly class GoogleResourceOwnerProvider implements GoogleResourceOwnerProviderInterface
{
    public function __construct(
        private GoogleClientFactoryInterface $googleClientFactory
    )
    {
    }

    public function getResourceOwner(string $code): Userinfo
    {
        $client = $this->googleClientFactory->createClient();
        $client->fetchAccessTokenWithAuthCode($code);
        $oauthClient = new Oauth2($client);

        return $oauthClient->userinfo->get();
    }
}
