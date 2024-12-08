<?php declare(strict_types=1);

namespace App\Service\ProcessSubmissionCollectionProvider;

use App\Entity\FormProcessSubmission;
use App\Repository\FormProcessSubmission\FormProcessSubmissionRepository;
use App\Service\FormIoClient\Dto\FormSubmission\FormSubmissionDto;
use App\Service\FormIoClient\Dto\GetSubmissionDto;
use App\Service\FormIoClient\FormIoClient;
use Throwable;

final readonly class ProcessSubmissionCollectionProvider
{
    public function __construct(
        private FormProcessSubmissionRepository   $processSubmissionRepository,
        private FormIoClient $formIoClient
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function getSubmissionsByIds(array $submissionIds): ProcessSubmissionCollection
    {
        $submissionIdEntities = $this->processSubmissionRepository->findByIds($submissionIds);
        return $this->createResultCollection($submissionIdEntities);
    }

    /**
     * @param FormProcessSubmission $submissionId
     * @return FormSubmissionDto
     * @throws Throwable
     */
    private function getSubmissionDto(FormProcessSubmission $submissionId): FormSubmissionDto
    {
        return $this->formIoClient->getSubmission(new GetSubmissionDto(
            formKey: $submissionId->getForm()->getFormKey(),
            submissionId: $submissionId->getSubmissionId()
        ));
    }

    /**
     * @param array $submissionIdEntities
     * @return ProcessSubmissionCollection
     * @throws Throwable
     */
    private function createResultCollection(array $submissionIdEntities): ProcessSubmissionCollection
    {
        $result = new ProcessSubmissionCollection();
        foreach ($submissionIdEntities as $submissionId) {
            $result->addSubmission(
                $this->getSubmissionDto($submissionId),
                $submissionId
            );
        }

        return $result;
    }
}
