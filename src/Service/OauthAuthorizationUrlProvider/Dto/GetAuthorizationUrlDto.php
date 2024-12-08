<?php declare(strict_types=1);

namespace App\Service\OauthAuthorizationUrlProvider\Dto;

use App\ArgumentResolver\FromRequestDtoInterface;
use App\Enum\OauthTypeEnum;

final class GetAuthorizationUrlDto implements FromRequestDtoInterface
{
    public OauthTypeEnum $oauthType;
    public ?string $state = null;
    public ?string $siteAlias = null;
}
