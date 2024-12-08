<?php declare(strict_types=1);

namespace App\Service\OauthGovIdProvider;

use App\Enum\OauthTypeEnum;
use App\Service\OauthCallbackUrlGenerator;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class GovIdProvider extends GenericProvider
{
    public function __construct(
        OauthCallbackUrlGenerator                                      $urlGenerator,
        #[Autowire(env: 'GOV_UA_CLIENT_ID')] string                    $clientId,
        #[Autowire(env: 'GOV_UA_CLIENT_SECRET')] string                $clientSecret,
        #[Autowire(env: 'resolve:GOV_UA_URL_AUTH')] string             $urlAuthorize,
        #[Autowire(env: 'GOV_UA_URL_ACCESS_TOKEN')] string             $urlAccessToken,
        #[Autowire(env: 'GOV_UA_URL_RESOURCE_OWNER_DETAILS')] string   $urlResourceOwnerDetails,
        #[Autowire(env: 'resolve:GOV_UA_CERTIFICATE')] private readonly string $publicCertPath
    )
    {
        parent::__construct([
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'redirectUri' => $urlGenerator->generateCallbackUrl(OauthTypeEnum::GOV_ID),
            'urlAuthorize' => $urlAuthorize,
            'urlAccessToken' => $urlAccessToken,
            'urlResourceOwnerDetails' => $urlResourceOwnerDetails
        ]);
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        $cert = file_get_contents($this->publicCertPath, true);
        $cert = base64_encode($cert);
        $cert = urlencode($cert);

        $t = $token->getToken();
        $u_id = $token->getValues()['user_id'];

        return parent::getResourceOwnerDetailsUrl($token) . "?access_token=$t&user_id=$u_id&cert=" . $cert;
    }
}
