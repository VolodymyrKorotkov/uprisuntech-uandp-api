<?php declare(strict_types=1);

namespace App\Service\OauthAuthorizationUrlProvider;

use App\Service\OauthAuthorizationUrlProvider\Dto\GetAuthorizationUrlDto;
use App\Service\OauthStateResolver\OauthStateResolverInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator(OauthAuthorizationUrlProviderInterface::class)]
final readonly class UrlProviderEncodeStateDecorator implements OauthAuthorizationUrlProviderInterface
{
    private OauthAuthorizationUrlProviderInterface $provider;
    private OauthStateResolverInterface $stateEncoder;

    public function __construct(
        OauthAuthorizationUrlProviderInterface $provider,
        OauthStateResolverInterface $stateEncoder
    )
    {
        $this->provider = $provider;
        $this->stateEncoder = $stateEncoder;
    }

    public function getAuthorizationUrl(GetAuthorizationUrlDto $dto): string
    {
        $dto->state = $this->stateEncoder->createOauthState($dto);

        return $this->provider->getAuthorizationUrl($dto);
    }
}
