<?php

namespace App\Service\AggregateTaskCompleter;

use App\Service\AggregateTaskFactory\AggregateTaskFactory;
use App\Service\AggregateTaskProvider\Dto\AggregateTaskDto;
use App\Service\CamundaTaskCompleter\CamundaTaskWithSubmissionCompleter;
use App\Service\CamundaTaskProvider\CamundaTaskWithSubmissionProvider;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\NonUniqueResultException;

final readonly class CamundaTaskCompleteAdapter implements TaskCompleteAdapterInterface
{
    public function __construct(
        private CamundaTaskWithSubmissionProvider  $taskProvider,
        private CamundaTaskWithSubmissionCompleter $taskCompleter,
        private AggregateTaskFactory $taskFactory
    )
    {
    }

    /**
     * @throws \Throwable
     * @throws NonUniqueResultException
     * @throws EntityNotFoundException
     */
    public function completeTask(CompleteTaskDto $dto): AggregateTaskDto
    {
        $taskWithSubmission = $this->taskProvider->getTaskWithProcessSubmission($dto->getId());
        $this->taskCompleter->completeTaskWithSubmission($taskWithSubmission);

        return $this->taskFactory->createTaskFromCamunda(null, $taskWithSubmission->camundaTask);
    }
}
