<?php declare(strict_types=1);

namespace App\Controller\ApiPlatform;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Serializer\AppJsonNormalizerCopyInterface;
use App\Service\AggregateTaskProvider\Dto\ProcessTasksCollection;
use App\Service\AggregateTaskProvider\Dto\TasksFromAllSourcesFilterDto;
use App\Service\AggregateTaskProvider\TaskSourcesAggregateProviderInterface;

final readonly class AggregateTaskCollectionProvider implements ProviderInterface
{
    public function __construct(
        private TaskSourcesAggregateProviderInterface $taskAllSourcesAggregateProvider,
        private AppJsonNormalizerCopyInterface $appJsonNormalizer
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ProcessTasksCollection
    {
        $filter = $this->getFilter($context);

        if (!$filter->hasTypeId()){
            return new ProcessTasksCollection(fn() => [], 0);
        }

        return $this->taskAllSourcesAggregateProvider->getTasksFromAllSources($filter);
    }

    /**
     * @param $context
     * @return TasksFromAllSourcesFilterDto
     */
    public function getFilter($context): TasksFromAllSourcesFilterDto
    {
        return $this->appJsonNormalizer->denormalize(
            data: $context['filters'] ?? [],
            type: TasksFromAllSourcesFilterDto::class
        );
    }
}
