<?php declare(strict_types=1);

namespace App\Service\ProcessSubmissionVariable;

use App\Entity\Resource\ProcessSubmissionVariable;
use App\Repository\FormProcessSubmissionVariable\FormProcessSubmissionVariableRepository;
use App\Service\FormIoClient\Dto\EditSubmissionDto;
use App\Service\FormIoClient\FormIoClient;
use App\Service\ProcessSubmissionCollectionProvider\ProcessSubmissionCollectionProvider;
use Throwable;

final readonly class ProcessSubmissionVariableValuesSaver
{
    public function __construct(
        private FormProcessSubmissionVariableRepository $variableRepository,
        private ProcessSubmissionCollectionProvider $processSubmissionAllFormsProvider,
        private FormIoClient $formIoClient
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function saveProcessVariableValues(ProcessSubmissionVariable $dto): void
    {
        $existsVars = $this->variableRepository->findForProcess($dto->processInstanceId, $dto->varKeys());
        $submissions = $this->processSubmissionAllFormsProvider->getSubmissionsByIds(
            $existsVars->getSubmissionIds()
        );

        foreach ($existsVars as $variable){
            $submissions->getSubmission($variable->getSubmissionId())->data[$variable->getSubmissionProperty()] = $dto->getVariableValue($variable->getKey());
        }

        foreach ($submissions as $submission){
            $formSubmission = $submissions->getFormProcessSubmission($submission->id);

            $this->formIoClient->editSubmission(
                new EditSubmissionDto(
                    formKey: $formSubmission->getForm()->getFormKey(),
                    submissionId: $submission->id,
                    data: $submission
                )
            );
        }
    }
}
