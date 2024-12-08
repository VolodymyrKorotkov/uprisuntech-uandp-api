<?php declare(strict_types=1);

namespace App\Service\OauthCallbackHandler\Dto;

use App\ArgumentResolver\FromRequestDtoInterface;
use App\Enum\OauthTypeEnum;
use Symfony\Component\Validator\Constraints\NotBlank;

final class HandleOauthCallbackDto implements FromRequestDtoInterface
{
    #[NotBlank]
    public string $code;
    public OauthTypeEnum $oauthType;
    public ?string $state = null;
}
