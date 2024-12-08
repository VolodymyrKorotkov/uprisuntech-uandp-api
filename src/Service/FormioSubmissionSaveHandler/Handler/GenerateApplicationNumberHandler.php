<?php declare(strict_types=1);

namespace App\Service\FormioSubmissionSaveHandler\Handler;

use App\Service\FormIoClient\Dto\FormSubmission\FormSubmissionDto;
use App\Service\FormioSubmissionSaveHandler\Dto\HandleSubmissionSaveDto;
use App\Service\FormioSubmissionSaveHandler\Dto\HandleSubmissionSaveRequestDto;
use App\Service\ProcessSubmissionVariable\SubmissionPropertyAccessor;

final readonly class GenerateApplicationNumberHandler implements FormioSubmissionSaveHandlerInterface
{
    public function __construct(
        private SubmissionPropertyAccessor $submissionPropertyAccessor,
    )
    {
    }

    public function handle(HandleSubmissionSaveDto $dto): HandleSubmissionSaveRequestDto
    {
        if (!$dto->formIo->isApplicationPublicForm()) {
            return $dto->formioWebHookRequest;
        }

        $dto->formioWebHookRequest->submission = $this->setApplicationNumber($dto);

        return $dto->formioWebHookRequest;
    }

    private function generateApplicationNumber(): string
    {
        $today = date("Ymd");
        $rand = strtoupper(
            substr(
                uniqid(sha1((string)time())),
                0,
                4
            )
        );

        return $today . $rand;
    }

    /**
     * @param HandleSubmissionSaveDto $dto
     * @return FormSubmissionDto
     */
    private function setApplicationNumber(HandleSubmissionSaveDto $dto): FormSubmissionDto
    {
        $submission = $dto->formioWebHookRequest->submission;
        $this->submissionPropertyAccessor->setApplicationNumber(
            submission: $submission,
            formIo: $dto->formIo,
            number: $this->generateApplicationNumber()
        );

        return $submission;
    }
}
