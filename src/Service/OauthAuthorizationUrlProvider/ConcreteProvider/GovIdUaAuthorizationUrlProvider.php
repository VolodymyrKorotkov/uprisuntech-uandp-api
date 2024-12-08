<?php declare(strict_types=1);

namespace App\Service\OauthAuthorizationUrlProvider\ConcreteProvider;

use App\Enum\OauthTypeEnum;
use App\Service\OauthAuthorizationUrlProvider\ConcreteAuthorizationUrlProviderInterface;
use App\Service\OauthAuthorizationUrlProvider\Dto\GetAuthorizationUrlDto;
use App\Service\OauthGovIdProvider\GovIdProvider;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class GovIdUaAuthorizationUrlProvider implements ConcreteAuthorizationUrlProviderInterface
{
    public function __construct(
        #[Autowire(env: 'GOV_UA_AUTH_TYPE')] private string $govUaAuthType,
        private GovIdProvider $govIdUaProvider
    )
    {
    }

    public function getProviderType(): OauthTypeEnum
    {
        return OauthTypeEnum::GOV_ID;
    }

    public function getAuthorizationUrl(GetAuthorizationUrlDto $dto): string
    {
        return $this->govIdUaProvider->getAuthorizationUrl([
            'state' => $dto->state,
            'auth_type' => $this->govUaAuthType
        ]);
    }
}
