<?php declare(strict_types=1);

namespace App\Service\FormioSubmissionSaveHandler\Handler;

use App\Service\FormioSubmissionSaveHandler\Dto\HandleSubmissionSaveDto;
use App\Service\FormioSubmissionSaveHandler\Dto\HandleSubmissionSaveRequestDto;
use App\Service\ProcessSubmissionIdLinker\Dto\SaveResourcesForProcessFromSubmissionDto;
use App\Service\ProcessSubmissionIdLinker\ProcessResourceSubmissionResolver;
use App\Service\ProcessSubmissionIdLinker\ProcessSubmissionIdLinker;

final readonly class SaveResourcesAfterEditHandler implements FormioSubmissionSaveHandlerInterface
{
    public function __construct(
        private ProcessResourceSubmissionResolver $processResourceSubmissionResolver,
        private ProcessSubmissionIdLinker         $processSubmissionIdLinker
    )
    {}

    public function handle(HandleSubmissionSaveDto $dto): HandleSubmissionSaveRequestDto
    {
        $processesIds = $this->processSubmissionIdLinker->getProcessesIdsForSubmission(
            $dto->formioWebHookRequest->submission->id
        );

        foreach ($processesIds as $processesId){
            $this->processResourceSubmissionResolver->saveResourcesForProcessFromSubmission(
                new SaveResourcesForProcessFromSubmissionDto(
                    processInstanceId: $processesId,
                    formIo: $dto->formIo,
                    submission: $dto->formioWebHookRequest->submission
                )
            );
        }

        return $dto->formioWebHookRequest;
    }
}
