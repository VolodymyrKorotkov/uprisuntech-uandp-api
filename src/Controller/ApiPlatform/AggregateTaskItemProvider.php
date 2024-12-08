<?php declare(strict_types=1);

namespace App\Controller\ApiPlatform;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Serializer\AppJsonNormalizerCopyInterface;
use App\Service\AggregateTaskProvider\Dto\AggregateTaskDto;
use App\Service\AggregateTaskProvider\Dto\GetOneTaskFromAllSourcesDto;
use App\Service\AggregateTaskProvider\TaskSourcesAggregateProviderInterface;

final readonly class AggregateTaskItemProvider implements ProviderInterface
{
    public function __construct(
        private TaskSourcesAggregateProviderInterface $sourcesAggregateProvider,
        private AppJsonNormalizerCopyInterface $appJsonNormalizer
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AggregateTaskDto
    {
        return $this->sourcesAggregateProvider->getOneTaskFromAllSources(
            $this->appJsonNormalizer->denormalize($uriVariables, type: GetOneTaskFromAllSourcesDto::class)
        )->task;
    }
}
