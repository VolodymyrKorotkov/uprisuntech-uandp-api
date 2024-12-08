<?php declare(strict_types=1);

namespace App\Service\FormioSubmissionSaveHandler\Handler;

use App\Service\FormioSubmissionSaveHandler\Dto\HandleSubmissionSaveDto;
use App\Service\FormioSubmissionSaveHandler\Dto\HandleSubmissionSaveRequestDto;
use App\Service\FormioSubmissionSaveHandler\SubmissionStatusChecker;
use App\Service\FormSubmissionEditLockerService;

final readonly class LockSubmissionEditHandler implements FormioSubmissionSaveHandlerInterface
{
    public function __construct(
        private FormSubmissionEditLockerService $lockerService,
        private SubmissionStatusChecker $submissionStatusChecker
    )
    {
    }

    public function handle(HandleSubmissionSaveDto $dto): HandleSubmissionSaveRequestDto
    {
        if ($this->submissionStatusChecker->isConfirmedStatus($dto->formioWebHookRequest->submission, $dto->formIo)){
            $this->lockerService->lockSubmissionForEdit($dto->formioWebHookRequest->submission->id);
        }

        return $dto->formioWebHookRequest;
    }
}
