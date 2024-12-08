<?php

namespace App\Service\OauthAuthorizationUrlProvider;

use App\Service\OauthAuthorizationUrlProvider\Dto\GetAuthorizationUrlDto;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

#[AsAlias]
final class OauthAuthorizationUrlProvider implements OauthAuthorizationUrlProviderInterface
{
    /**
     * @var array<ConcreteAuthorizationUrlProviderInterface>
     */
    private array $providers;

    public function __construct(
        /**
         * @var $providers array<ConcreteAuthorizationUrlProviderInterface>
         */
        #[TaggedIterator(ConcreteAuthorizationUrlProviderInterface::class)]
        iterable $providers
    )
    {
        foreach ($providers as $provider) {
            $this->providers[$provider->getProviderType()->value] = $provider;
        }
    }

    public function getAuthorizationUrl(GetAuthorizationUrlDto $dto): string
    {
        return $this->providers[$dto->oauthType->value]->getAuthorizationUrl($dto);
    }
}
