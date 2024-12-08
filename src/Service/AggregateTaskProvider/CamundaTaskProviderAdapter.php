<?php declare(strict_types=1);

namespace App\Service\AggregateTaskProvider;

use App\Enum\ApplicationStrategyEnum;
use App\Service\AggregateTaskFactory\AggregateTaskFactory;
use App\Service\AggregateTaskProvider\Dto\AggregateTaskDto;
use App\Service\AggregateTaskProvider\Dto\GetOneTaskFromAllSourcesDto;
use App\Service\AggregateTaskProvider\Dto\TasksSourceFilterDto;
use App\Service\CamundaTaskProvider\CamundaTaskByFilterProvider;
use App\Service\CamundaTaskProvider\GetTaskListFilterDto;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class CamundaTaskProviderAdapter implements TaskProviderAdapterInterface
{
    public function __construct(
        private CamundaTaskByFilterProvider $camundaTaskByFilterProvider,
        private AggregateTaskFactory $aggregateProcessTaskFactory
    )
    {
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getSourceTasks(TasksSourceFilterDto $filterDto): array
    {
        $tasks = $this->camundaTaskByFilterProvider->getTasks(new GetTaskListFilterDto(
            typeId: $filterDto->getApplicationType()->getId()
        ));

        $result = [];
        foreach ($tasks as $task) {
            $result[] = $this->aggregateProcessTaskFactory->createTaskFromCamundaForList(
                $filterDto->getApplicationType(),
                $task
            );
        }

        return $result;
    }

    public function getStrategyType(): ApplicationStrategyEnum
    {
        return ApplicationStrategyEnum::CAMUNDA;
    }

    public function getCount(TasksSourceFilterDto $filterDto): int
    {
        return 10;
    }

    public function getOneSourceTask(GetOneTaskFromAllSourcesDto $filterDto): AggregateTaskDto
    {
        try {
            $task = $this->camundaTaskByFilterProvider->getTask($filterDto->getTaskId());
        } catch (ClientException $throwable) {
            if ($throwable->getResponse()->getStatusCode() === 404) {
                throw new NotFoundHttpException();
            } else {
                throw $throwable;
            }
        }

        return $this->aggregateProcessTaskFactory->createTaskFromCamunda(null, $task);
    }
}
