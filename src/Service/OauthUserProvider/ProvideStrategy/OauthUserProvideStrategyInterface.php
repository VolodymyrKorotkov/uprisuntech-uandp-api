<?php

namespace App\Service\OauthUserProvider\ProvideStrategy;

use App\Service\OauthUserProvider\Dto\GetOauthUserDto;
use App\Service\OauthUserProvider\Dto\GetOauthUserResult;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(OauthUserProvideStrategyInterface::class)]
interface OauthUserProvideStrategyInterface
{
    public function handleOauthUser(GetOauthUserDto $dto): GetOauthUserResult;
    public function support(GetOauthUserDto $dto): bool;
}
