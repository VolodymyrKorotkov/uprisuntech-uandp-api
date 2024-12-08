<?php declare(strict_types=1);

namespace App\Service\FormioSubmissionSaveHandler\Handler;

use App\Service\FormIoClient\Dto\CreateSubmissionDto;
use App\Service\FormIoClient\Dto\EditSubmissionDto;
use App\Service\FormIoClient\FormIoClient;
use App\Service\FormioSubmissionSaveHandler\Dto\HandleSubmissionSaveRequestDto;
use App\Service\FormioSubmissionSaveHandler\Dto\HandleSubmissionSaveDto;
use Throwable;

final readonly class SaveSubmissionHandler implements FormioSubmissionSaveHandlerInterface
{
    public function __construct(
        private FormIoClient $formIoClient
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function handle(HandleSubmissionSaveDto $dto): HandleSubmissionSaveRequestDto
    {
        $submission = $dto->formioWebHookRequest->submission;

        if ($submission->id){
            $this->formIoClient->editSubmission(
                new EditSubmissionDto(
                    formKey: $dto->formIo->getFormKey(),
                    submissionId: $submission->id,
                    data: $submission
                )
            );
        } else {
            $dto->formioWebHookRequest->submission = $this->formIoClient->createSubmission(
                new CreateSubmissionDto(
                    formKey: $dto->formIo->getFormKey(),
                    data: $submission
                )
            );
        }

        return $dto->formioWebHookRequest;
    }
}
