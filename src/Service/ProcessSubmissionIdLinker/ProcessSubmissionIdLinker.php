<?php declare(strict_types=1);

namespace App\Service\ProcessSubmissionIdLinker;

use App\Entity\FormProcessSubmission;
use App\Entity\FormTaskSubmission;
use App\Repository\FormProcessSubmission\FormProcessSubmissionRepository;
use App\Repository\FormTaskSubmission\FormTaskSubmissionRepository;
use App\Service\FormIoClient\Dto\FormSubmission\FormSubmissionDto;
use App\Service\FormIoClient\Dto\GetSubmissionDto;
use App\Service\FormIoClient\FormIoClient;
use App\Service\ProcessSubmissionIdLinker\Dto\CreateSubmissionFromProcessResources;
use App\Service\ProcessSubmissionIdLinker\Dto\GetSubmissionIdForProcessDto;
use App\Service\ProcessSubmissionIdLinker\Dto\GetSubmissionIdForTaskDto;
use App\Service\ProcessSubmissionIdLinker\Dto\LinkSubmissionWithProcessDto;
use App\Service\ProcessSubmissionIdLinker\Dto\SaveResourcesForProcessFromSubmissionDto;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\NonUniqueResultException;
use Throwable;

final readonly class ProcessSubmissionIdLinker
{
    public function __construct(
        private FormProcessSubmissionRepository   $processSubmissionRepository,
        private FormTaskSubmissionRepository      $taskSubmissionRepository,
        private ProcessResourceSubmissionResolver $processResourceSubmissionResolver,
        private FormIoClient                      $formIoClient
    )
    {
    }

    public function linkSubmissionWithProcess(LinkSubmissionWithProcessDto $dto): FormProcessSubmission
    {
        $this->processResourceSubmissionResolver->saveResourcesForProcessFromSubmission(
            SaveResourcesForProcessFromSubmissionDto::newFromLinkSubmissionWithProcess($dto)
        );

        $newProcessSubmissionId = new FormProcessSubmission();
        $newProcessSubmissionId->setSubmissionId($dto->submission->id);
        $newProcessSubmissionId->setForm($dto->formIo);
        $newProcessSubmissionId->setProcessInstanceId($dto->processInstanceId);

        $this->processSubmissionRepository->save($newProcessSubmissionId);

        return $newProcessSubmissionId;
    }

    public function getProcessesIdsForSubmission(string $submissionId): array
    {
        $ids = array_merge(
            $this->taskSubmissionRepository->findForSubmission($submissionId)->getSubmissionIds(),
            $this->processSubmissionRepository->findForSubmission($submissionId)->getSubmissionIds(),
        );

        return array_unique($ids);
    }

    /**
     * @throws NonUniqueResultException
     * @throws Throwable
     */
    public function getSubmissionIdForProcess(GetSubmissionIdForProcessDto $dto): string
    {
        try {
            return $this->processSubmissionRepository
                ->getForProcessForm($dto->processInstanceId, $dto->formio->getFormKey())
                ->getSubmissionId();
        } catch (EntityNotFoundException $e) {
            return $this->createSubmissionForProcess($dto)->id;
        }
    }

    /**
     * @throws NonUniqueResultException
     * @throws Throwable
     */
    public function getSubmissionForProcess(GetSubmissionIdForProcessDto $dto): FormSubmissionDto
    {
        try {
            $submissionId = $this->processSubmissionRepository
                ->getForProcessForm($dto->processInstanceId, $dto->formio->getFormKey())
                ->getSubmissionId();

            return $this->formIoClient->getSubmission(
                new GetSubmissionDto(
                    formKey: $dto->formio->getFormKey(),
                    submissionId: $submissionId
                )
            );
        } catch (EntityNotFoundException $e) {
            return $this->createSubmissionForProcess($dto);
        }
    }

    /**
     * @throws NonUniqueResultException
     * @throws Throwable
     */
    public function getSubmissionIdForTask(GetSubmissionIdForTaskDto $dto): string
    {
        try {
            return $this->taskSubmissionRepository
                ->getForTask($dto->taskId, $dto->formio->getFormKey())
                ->getSubmissionId();
        } catch (EntityNotFoundException) {
            $submission = $this->processResourceSubmissionResolver->createSubmissionFromProcessResources(
                CreateSubmissionFromProcessResources::newFromGetSubmissionIdForTask($dto)
            );

            $newTaskSubmissionId = new FormTaskSubmission();
            $newTaskSubmissionId->setSubmissionId($submission->id);
            $newTaskSubmissionId->setForm($dto->formio);
            $newTaskSubmissionId->setProcessId($dto->processInstanceId);
            $newTaskSubmissionId->setTaskId($dto->taskId);

            $this->taskSubmissionRepository->save($newTaskSubmissionId);

            return $submission->id;
        }
    }

    /**
     * @param GetSubmissionIdForProcessDto $dto
     * @return FormSubmissionDto
     * @throws Throwable
     */
    private function createSubmissionForProcess(GetSubmissionIdForProcessDto $dto): FormSubmissionDto
    {
        $submission = $this->processResourceSubmissionResolver->createSubmissionFromProcessResources(
            CreateSubmissionFromProcessResources::newFromGetSubmissionIdForProcess($dto)
        );

        $newProcessSubmissionId = new FormProcessSubmission();
        $newProcessSubmissionId->setSubmissionId($submission->id);
        $newProcessSubmissionId->setForm($dto->formio);
        $newProcessSubmissionId->setProcessInstanceId($dto->processInstanceId);

        $this->processSubmissionRepository->save($newProcessSubmissionId);

        return $submission;
    }
}
