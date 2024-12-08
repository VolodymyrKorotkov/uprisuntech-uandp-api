<?php

namespace App\Service\OauthAuthorizationUrlProvider\ConcreteProvider;

use App\Enum\OauthTypeEnum;
use App\Service\OauthAuthorizationUrlProvider\ConcreteAuthorizationUrlProviderInterface;
use App\Service\OauthAuthorizationUrlProvider\Dto\GetAuthorizationUrlDto;
use App\Service\OauthGoogleProvider\GoogleClientFactoryInterface;

final readonly class GoogleAuthorizationUrlProvider implements ConcreteAuthorizationUrlProviderInterface
{
    public function __construct(
        private GoogleClientFactoryInterface $googleClientFactory
    )
    {
    }

    public function getProviderType(): OauthTypeEnum
    {
        return OauthTypeEnum::GOOGLE;
    }

    public function getAuthorizationUrl(GetAuthorizationUrlDto $dto): string
    {
        return $this->googleClientFactory->createClient()->createAuthUrl(
            null,
            ['state' => $dto->state]
        );
    }
}
