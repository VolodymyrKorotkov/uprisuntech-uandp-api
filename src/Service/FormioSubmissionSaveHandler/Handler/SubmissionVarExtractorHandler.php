<?php

namespace App\Service\FormioSubmissionSaveHandler\Handler;

use App\Repository\FormProcessSubmission\FormProcessSubmissionRepository;
use App\Service\FormioSubmissionSaveHandler\Dto\HandleSubmissionSaveDto;
use App\Service\FormioSubmissionSaveHandler\Dto\HandleSubmissionSaveRequestDto;
use App\Service\ProcessSubmissionVariable\ProcessSubmissionVariablesExtractor;
use Doctrine\ORM\EntityNotFoundException;
use Throwable;

final readonly class SubmissionVarExtractorHandler implements FormioSubmissionSaveHandlerInterface
{
    public function __construct(
        private FormProcessSubmissionRepository $formProcessSubmissionRepository,
        private ProcessSubmissionVariablesExtractor $variablesExtractor,
    )
    {
    }

    /**
     * @throws Throwable
     * @throws EntityNotFoundException
     */
    public function handle(HandleSubmissionSaveDto $dto): HandleSubmissionSaveRequestDto
    {
        if (!$dto->formioWebHookRequest->submission->id){
            return $dto->formioWebHookRequest;
        }

        try {
            $this->variablesExtractor->extractVarsFromFormComponentProperties(
                $this->formProcessSubmissionRepository->getBySubmissionId($dto->formioWebHookRequest->submission->id)
            );

            return $dto->formioWebHookRequest;
        } catch (EntityNotFoundException){
            return $dto->formioWebHookRequest;
        }
    }
}
