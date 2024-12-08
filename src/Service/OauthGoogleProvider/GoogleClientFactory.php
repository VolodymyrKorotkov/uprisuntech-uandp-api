<?php declare(strict_types=1);

namespace App\Service\OauthGoogleProvider;

use App\Enum\OauthTypeEnum;
use App\Service\OauthCallbackUrlGenerator;
use Google\Client;
use Google\Exception;
use Google\Service\Oauth2;

final readonly class GoogleClientFactory implements GoogleClientFactoryInterface
{
    public function __construct(
        private string                $projectDir,
        private OauthCallbackUrlGenerator                                      $urlGenerator,
    )
    {
    }

    /**
     * @throws Exception
     */
    public function createClient(): Client
    {
        $client = new Client();
        $client->setAuthConfig($this->projectDir . '/secret/google_oauth.json');
        $client->addScope([Oauth2::USERINFO_PROFILE, Oauth2::USERINFO_EMAIL]);
        $client->setPrompt('consent');
        $client->setRedirectUri(
            $this->urlGenerator->generateCallbackUrl(OauthTypeEnum::GOOGLE)
        );

        return $client;
    }
}
