<?php declare(strict_types=1);

namespace App\Service\CamundaTaskCompleter;

use App\Service\CamundaTaskProvider\CamundaTaskWithSubmissionDto;
use App\Service\CamundaTaskProvider\CamundaTaskWithSubmissionProvider;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\NonUniqueResultException;
use Throwable;

final readonly class CamundaTaskCompleter
{
    public function __construct(
        private CamundaTaskWithSubmissionProvider  $taskProvider,
        private CamundaTaskWithSubmissionCompleter $taskCompleter
    )
    {
    }

    /**
     * @throws Throwable
     * @throws NonUniqueResultException
     * @throws EntityNotFoundException
     */
    public function completeCamundaTask(CompleteCamundaTaskDto $dto): CamundaTaskWithSubmissionDto
    {
        $taskWithSubmission = $this->taskProvider->getTaskWithProcessSubmission($dto->taskId);
        $this->taskCompleter->completeTaskWithSubmission($taskWithSubmission);

        return $taskWithSubmission;
    }
}
