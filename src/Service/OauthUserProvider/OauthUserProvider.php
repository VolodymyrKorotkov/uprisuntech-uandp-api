<?php declare(strict_types=1);

namespace App\Service\OauthUserProvider;

use App\Service\OauthUserProvider\Dto\GetOauthUserDto;
use App\Service\OauthUserProvider\Dto\GetOauthUserResult;
use App\Service\OauthUserProvider\ProvideStrategy\OauthUserProvideStrategyInterface;
use Exception;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

#[AsAlias]
final readonly class OauthUserProvider implements OauthUserProviderInterface
{
    /**
     * @param iterable<OauthUserProvideStrategyInterface> $userProvideStrategies
     */
    public function __construct(
        #[TaggedIterator(OauthUserProvideStrategyInterface::class)] private iterable $userProvideStrategies
    )
    {
    }

    /**
     * @throws Exception
     */
    public function getOauthUser(GetOauthUserDto $dto): GetOauthUserResult
    {
        foreach ($this->userProvideStrategies as $userProvider) {
            if ($userProvider->support($dto)) {
                return $userProvider->handleOauthUser($dto);
            }
        }

        return throw new Exception(__METHOD__.'- Strategy not found!');
    }
}
