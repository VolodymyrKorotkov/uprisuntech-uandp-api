<?php

namespace App\Service\OauthAuthorizationUrlProvider;

use App\Enum\OauthTypeEnum;
use App\Service\OauthAuthorizationUrlProvider\Dto\GetAuthorizationUrlDto;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(ConcreteAuthorizationUrlProviderInterface::class)]
interface ConcreteAuthorizationUrlProviderInterface
{
    public function getProviderType(): OauthTypeEnum;
    public function getAuthorizationUrl(GetAuthorizationUrlDto $dto): string;
}
