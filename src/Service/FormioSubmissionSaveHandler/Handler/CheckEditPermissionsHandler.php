<?php declare(strict_types=1);

namespace App\Service\FormioSubmissionSaveHandler\Handler;

use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;
use App\Service\FormioSubmissionSaveHandler\Dto\HandleSubmissionSaveDto;
use App\Service\FormioSubmissionSaveHandler\Dto\HandleSubmissionSaveRequestDto;
use App\Service\FormSubmissionEditLockerService;

final readonly class CheckEditPermissionsHandler implements FormioSubmissionSaveHandlerInterface
{
    public function __construct(
        private FormSubmissionEditLockerService $lockerService
    )
    {
    }

    public function handle(HandleSubmissionSaveDto $dto): HandleSubmissionSaveRequestDto
    {
        if ($this->lockerService->isLocked($dto->formioWebHookRequest->submission->id)){
            throw new AccessDeniedException('Data cannot be edited');
        }

        return $dto->formioWebHookRequest;
    }
}
