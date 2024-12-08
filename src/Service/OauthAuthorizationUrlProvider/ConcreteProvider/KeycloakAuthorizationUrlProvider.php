<?php declare(strict_types=1);

namespace App\Service\OauthAuthorizationUrlProvider\ConcreteProvider;

use App\Enum\OauthTypeEnum;
use App\Service\OauthAuthorizationUrlProvider\ConcreteAuthorizationUrlProviderInterface;
use App\Service\OauthAuthorizationUrlProvider\Dto\GetAuthorizationUrlDto;
use App\Service\OauthKeycloakProvider\KeycloakProvider;

final readonly class KeycloakAuthorizationUrlProvider implements ConcreteAuthorizationUrlProviderInterface
{
    public function __construct(
        private KeycloakProvider $oauthProvider
    )
    {
    }

    public function getProviderType(): OauthTypeEnum
    {
        return OauthTypeEnum::KEYCLOAK;
    }

    public function getAuthorizationUrl(GetAuthorizationUrlDto $dto): string
    {
        return $this->oauthProvider->getAuthorizationUrl([
            'state' => $dto->state,
            'response_type' => 'code'
        ]);
    }
}
