<?php declare(strict_types=1);

namespace App\Service\ProcessSubmissionVariable;

use App\Entity\Resource\ProcessSubmissionVariable;
use App\Repository\ApplicationProcessRepository;
use App\Repository\FormProcessSubmissionVariable\FormProcessSubmissionVariableRepository;
use App\Service\ProcessSubmissionCollectionProvider\ProcessSubmissionCollectionProvider;
use Throwable;

final readonly class ProcessSubmissionVariablesValuesProvider
{
    public function __construct(
        private FormProcessSubmissionVariableRepository $variableRepository,
        private ProcessSubmissionCollectionProvider $processSubmissionAllFormsProvider,
        private ApplicationProcessRepository $applicationProcessRepository,
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function getProcessVariables(string $processId): ProcessSubmissionVariable
    {
        $existsVars = $this->variableRepository->findForProcess($processId);
        $submissions = $this->processSubmissionAllFormsProvider->getSubmissionsByIds(
            $existsVars->getSubmissionIds()
        );

        $result = new ProcessSubmissionVariable();
        $result->processInstanceId = $processId;
        $result->variables['processStarterUserIdentifier'] = $this->applicationProcessRepository->getByProcessID($processId)->getUser()->getUserIdentifier();

        foreach ($existsVars as $submissionVar){
            $submission = $submissions->getSubmission($submissionVar->getSubmissionId());
            $result->variables[$submissionVar->getKey()] = $submission->data[$submissionVar->getSubmissionProperty()];
        }

        return $result;
    }
}
