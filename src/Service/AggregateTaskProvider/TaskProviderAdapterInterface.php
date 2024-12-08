<?php declare(strict_types=1);

namespace App\Service\AggregateTaskProvider;

use App\Enum\ApplicationStrategyEnum;
use App\Service\AggregateTaskProvider\Dto\AggregateTaskDto;
use App\Service\AggregateTaskProvider\Dto\GetOneTaskFromAllSourcesDto;
use App\Service\AggregateTaskProvider\Dto\TasksSourceFilterDto;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(TaskProviderAdapterInterface::class)]
interface TaskProviderAdapterInterface
{
    /**
     * @return AggregateTaskDto[]
     */
    public function getSourceTasks(TasksSourceFilterDto $filterDto): array;
    public function getOneSourceTask(GetOneTaskFromAllSourcesDto $filterDto): AggregateTaskDto;
    public function getCount(TasksSourceFilterDto $filterDto): int;
    public function getStrategyType(): ApplicationStrategyEnum;
}
