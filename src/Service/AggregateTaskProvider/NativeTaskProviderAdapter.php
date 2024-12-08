<?php

namespace App\Service\AggregateTaskProvider;

use App\Enum\ApplicationStrategyEnum;
use App\Repository\ApplicationTask\NativeTaskRepository;
use App\Service\AggregateTaskFactory\AggregateTaskFactory;
use App\Service\AggregateTaskProvider\Dto\AggregateTaskDto;
use App\Service\AggregateTaskProvider\Dto\GetOneTaskFromAllSourcesDto;
use App\Service\AggregateTaskProvider\Dto\TasksSourceFilterDto;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class NativeTaskProviderAdapter implements TaskProviderAdapterInterface
{
    public function __construct(
        private NativeTaskRepository $applicationTaskRepository,
        private AggregateTaskFactory $aggregateProcessTaskFactory
    )
    {
    }

    public function getCount(TasksSourceFilterDto $filterDto): int
    {
        return $this->applicationTaskRepository->getSourceTasks($filterDto)->count();
    }

    public function getSourceTasks(TasksSourceFilterDto $filterDto): array
    {
        $result = [];
        $tasks = $this->applicationTaskRepository->getSourceTasks($filterDto);
        foreach ($tasks as $task){
            $result[] = $this->aggregateProcessTaskFactory->createTaskDtoFromNative($task);
        }

        return $result;
    }

    public function getStrategyType(): ApplicationStrategyEnum
    {
        return ApplicationStrategyEnum::NATIVE;
    }

    public function getOneSourceTask(GetOneTaskFromAllSourcesDto $filterDto): AggregateTaskDto
    {
        try {
            $task = $this->applicationTaskRepository->getByTaskId($filterDto->getTaskId());
        } catch (EntityNotFoundException) {
            throw new NotFoundHttpException();
        }

        return $this->aggregateProcessTaskFactory->createTaskDtoFromNative($task);
    }
}
