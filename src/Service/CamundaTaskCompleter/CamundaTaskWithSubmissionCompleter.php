<?php declare(strict_types=1);

namespace App\Service\CamundaTaskCompleter;

use App\Service\CamundaClient\CamundaClientInterface;
use App\Service\CamundaClient\Dto\CompleteTaskDto;
use App\Service\CamundaTaskProvider\CamundaTaskWithSubmissionDto;
use App\Service\ProcessSubmissionVariable\ProcessSubmissionVariablesValuesProvider;
use Doctrine\ORM\EntityNotFoundException;
use Throwable;

final readonly class CamundaTaskWithSubmissionCompleter
{
    public function __construct(
        private CamundaClientInterface          $camundaClient,
        private ProcessSubmissionVariablesValuesProvider $variablesValuesProvider
    )
    {
    }

    /**
     * @throws EntityNotFoundException
     * @throws Throwable
     */
    public function completeTaskWithSubmission(CamundaTaskWithSubmissionDto $dto): void
    {
        $this->camundaClient->completeTask(
            new CompleteTaskDto(
                processInstanceId: $dto->camundaTask->processInstanceId,
                taskId: $dto->camundaTask->id,
                variables: $this->variablesValuesProvider->getProcessVariables($dto->camundaTask->processInstanceId)->variables
            )
        );
    }
}
