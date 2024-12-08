<?php

namespace App\Service\CamundaTaskProvider;

use App\Service\AggregateProcessStarter\GetProcessSubmissionIdResult;
use App\Service\FormioGetOrCreateProvider;
use App\Service\ProcessSubmissionIdLinker\Dto\GetSubmissionIdForTaskDto;
use App\Service\ProcessSubmissionIdLinker\ProcessSubmissionIdLinker;
use Doctrine\ORM\NonUniqueResultException;
use Throwable;

final readonly class CamundaTaskWithSubmissionProvider implements CamundaTaskProviderInterface
{
    public function __construct(
        private CamundaTaskByFilterProviderInterface $camundaTaskByFilterProvider,
        private ProcessSubmissionIdLinker            $linker,
        private FormioGetOrCreateProvider            $formIoProvider,
    )
    {
    }

    /**
     * @throws Throwable
     * @throws NonUniqueResultException
     */
    public function getTaskWithProcessSubmission(string $taskId): CamundaTaskWithSubmissionDto
    {
        $taskDto = $this->camundaTaskByFilterProvider->getTask($taskId);
        $form = $this->formIoProvider->getOrCreateByFormKey($taskDto->getFormKeyLowerCase());

        $taskSubmissionId = $this->linker->getSubmissionIdForTask(
            GetSubmissionIdForTaskDto::newFromCamundaTask($taskDto, $form)
        );

        return new CamundaTaskWithSubmissionDto(
            camundaTask: $taskDto,
            processSubmission: new GetProcessSubmissionIdResult(
                formIo: $form,
                submissionId: $taskSubmissionId
            )
        );
    }
}
