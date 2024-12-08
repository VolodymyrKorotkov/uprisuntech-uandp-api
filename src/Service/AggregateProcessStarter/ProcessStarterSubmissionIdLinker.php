<?php declare(strict_types=1);

namespace App\Service\AggregateProcessStarter;

use App\Entity\ApplicationTask;
use App\Entity\FormProcessSubmission;
use App\Service\CamundaClient\Dto\CamundaTaskDto;
use App\Service\FormIoClient\Dto\GetSubmissionDto;
use App\Service\FormIoClient\FormIoClient;
use App\Service\FormioGetOrCreateProvider;
use App\Service\ProcessSubmissionIdLinker\Dto\LinkSubmissionWithProcessDto;
use App\Service\ProcessSubmissionIdLinker\ProcessSubmissionIdLinker;
use Doctrine\ORM\NonUniqueResultException;
use Throwable;

final readonly class ProcessStarterSubmissionIdLinker
{
    public function __construct(
        private FormioGetOrCreateProvider       $formioGetOrCreateProvider,
        private ProcessSubmissionIdLinker       $processSubmissionIdLinker,
        private FormIoClient                    $formIoClient
    )
    {
    }

    /**
     * @throws Throwable
     * @throws NonUniqueResultException
     */
    public function updateOrCreateByNativeTask(ApplicationTask $applicationTask, string $submissionId): void
    {
        $submission = $this->formIoClient->getSubmission(
            new GetSubmissionDto(
                formKey: $applicationTask->getForm()->getFormKey(),
                submissionId: $submissionId
            )
        );

        $this->processSubmissionIdLinker->linkSubmissionWithProcess(
            LinkSubmissionWithProcessDto::newFromApplicationTask($applicationTask, $submission)
        );
    }

    /**
     * @throws Throwable
     */
    public function updateOrCreateByCamundaTask(CamundaTaskDto $applicationTask, string $submissionId): FormProcessSubmission
    {
        $form = $this->formioGetOrCreateProvider->getOrCreateByFormKey($applicationTask->getFormKeyLowerCase());
        $submission = $this->formIoClient->getSubmission(
            new GetSubmissionDto(
                formKey: $form->getFormKey(),
                submissionId: $submissionId
            )
        );

        return $this->processSubmissionIdLinker->linkSubmissionWithProcess(
            new LinkSubmissionWithProcessDto(
                processInstanceId: $applicationTask->processInstanceId,
                formIo: $form,
                submission: $submission
            )
        );
    }
}
