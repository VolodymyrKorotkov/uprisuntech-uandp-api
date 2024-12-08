<?php declare(strict_types=1);

namespace App\Service\ProcessSubmissionIdLinker\Dto;

use App\Entity\ApplicationTask;
use App\Entity\FormIo;
use App\Service\FormIoClient\Dto\FormSubmission\FormSubmissionDto;
use App\Service\FormioSubmissionSaveHandler\Dto\HandleSubmissionSaveDto;

final readonly class LinkSubmissionWithProcessDto
{
    public function __construct(
        public string $processInstanceId,
        public FormIo $formIo,
        public FormSubmissionDto $submission
    )
    {
    }

    public static function newFromApplicationTask(ApplicationTask $applicationTask, FormSubmissionDto $submission): LinkSubmissionWithProcessDto
    {
        return new self(
            processInstanceId: $applicationTask->getProcessInstanceId(),
            formIo: $applicationTask->getForm(),
            submission: $submission
        );
    }

    public static function newFromHandleSubmissionSave(string $processId, HandleSubmissionSaveDto $handleSubmissionSaveDto): LinkSubmissionWithProcessDto
    {
        return new self(
            processInstanceId: $processId,
            formIo: $handleSubmissionSaveDto->formIo,
            submission: $handleSubmissionSaveDto->formioWebHookRequest->submission
        );
    }
}
