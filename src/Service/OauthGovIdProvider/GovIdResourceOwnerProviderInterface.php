<?php

namespace App\Service\OauthGovIdProvider;

use App\Service\OauthGovIdProvider\Dto\GetGovIdResourceOwnerDto;
use App\Service\OauthGovIdProvider\Dto\GovIdResourceOwnerDto;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(GovIdResourceOwnerProviderInterface::class)]
interface GovIdResourceOwnerProviderInterface
{
    public function getResourceOwner(GetGovIdResourceOwnerDto $dto): GovIdResourceOwnerDto;
}
