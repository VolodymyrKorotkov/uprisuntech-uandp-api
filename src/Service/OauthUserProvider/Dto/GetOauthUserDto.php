<?php declare(strict_types=1);

namespace App\Service\OauthUserProvider\Dto;

use App\ArgumentResolver\FromRequestDtoInterface;
use App\Enum\OauthTypeEnum;
use App\UserService\Service\Depr\OauthUserCreatorDepr\Dto\GetOrCreateUserDto;

final readonly class GetOauthUserDto implements FromRequestDtoInterface
{
    public function __construct(
        public string $code,
        public OauthTypeEnum $oauthType
    )
    {
    }

    public static function newFromGetOrCreateUser(GetOrCreateUserDto $dto): self
    {
        return new self(
            code: $dto->code,
            oauthType: $dto->oauthType
        );
    }
}
