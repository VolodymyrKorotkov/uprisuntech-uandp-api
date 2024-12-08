<?php

namespace App\Service\FormioSubmissionSaveHandler\Handler;

use App\Service\FormioSubmissionSaveHandler\Dto\HandleSubmissionSaveRequestDto;
use App\Service\FormioSubmissionSaveHandler\Dto\HandleSubmissionSaveDto;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(FormioSubmissionSaveHandlerInterface::class)]
interface FormioSubmissionSaveHandlerInterface
{
    public const SORTED_SUBMISSION_CREATE_HANDLERS = [
        GenerateApplicationNumberHandler::class,
        CreateNewUserFromApplicationHandler::class,
        //EditProposalHandler::class,
        SaveSubmissionHandler::class,
        StartApplicationProposalProcessHandler::class,
        StartProcessHandler::class
     //   SubmissionVarExtractorHandler::class
    ];

    public const SORTED_SUBMISSION_EDIT_HANDLERS = [
        CheckEditPermissionsHandler::class,
      //  EditProposalHandler::class,
        SaveSubmissionHandler::class,
        SaveResourcesAfterEditHandler::class,
        LockSubmissionEditHandler::class,
        SendUserEmailAfterEditManagerQuotaHandler::class,
        UnlockQuotaEditSubmissionAfterApplicationEditHandler::class
       // SubmissionVarExtractorHandler::class
    ];

    public function handle(HandleSubmissionSaveDto $dto): HandleSubmissionSaveRequestDto;
}
