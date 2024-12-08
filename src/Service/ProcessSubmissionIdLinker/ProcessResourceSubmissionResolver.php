<?php declare(strict_types=1);

namespace App\Service\ProcessSubmissionIdLinker;

use App\Entity\FormIo;
use App\Entity\FormIoProcessResource;
use App\Entity\FormProcessSubmission;
use App\Repository\FormProcessSubmission\FormProcessSubmissionCollection;
use App\Repository\FormProcessSubmission\FormProcessSubmissionRepository;
use App\Serializer\AppJsonNormalizerCopyInterface;
use App\Service\FormIoClient\Dto\CreateSubmissionDto;
use App\Service\FormIoClient\Dto\FormSubmission\FormSubmissionDto;
use App\Service\FormIoClient\Dto\GetSubmissionDto;
use App\Service\FormIoClient\FormIoSuperAdminClient;
use App\Service\ProcessSubmissionIdLinker\Dto\CreateSubmissionFromProcessResources;
use App\Service\ProcessSubmissionIdLinker\Dto\SaveResourcesForProcessFromSubmissionDto;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Throwable;

final readonly class ProcessResourceSubmissionResolver
{
    private PropertyAccessor $propertyAccessor;

    public function __construct(
        private AppJsonNormalizerCopyInterface  $appJsonNormalizer,
        private FormProcessSubmissionRepository $processSubmissionRepository,
        private FormIoSuperAdminClient          $formIoClient
    )
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    public function saveResourcesForProcessFromSubmission(SaveResourcesForProcessFromSubmissionDto $dto): void
    {
        $existsProcessSubmissionIds = $this->processSubmissionRepository->findForProcess($dto->processInstanceId);
        $data = $this->appJsonNormalizer->normalize($dto->submission->data);

        foreach ($dto->formIo->getProcessResources() as $processResource) {
            $this->saveResourceSubmission($dto->processInstanceId, $existsProcessSubmissionIds, $data, $processResource);
        }
    }

    /**
     * @throws Throwable
     */
    public function createSubmissionFromProcessResources(CreateSubmissionFromProcessResources $dto): FormSubmissionDto
    {
        $existsProcessSubmissionIds = $this->processSubmissionRepository->findForProcess($dto->processInstanceId);

        $submission = new FormSubmissionDto();
        $submission->asDraft();

        foreach ($dto->formIo->getProcessResources() as $processResource) {
            $submission = $this->setResourceSubmission($dto, $existsProcessSubmissionIds, $processResource, $submission);
        }

        return $this->formIoClient->createSubmission(
            new CreateSubmissionDto(
                formKey: $dto->formIo->getFormKey(),
                data: $submission
            )
        );
    }

    /**
     * @param FormProcessSubmissionCollection $existsProcessSubmissionIds
     * @param FormIoProcessResource $processResource
     * @param FormSubmissionDto $submission
     * @return FormSubmissionDto
     * @throws Throwable
     */
    private function setResourceSubmission(
        CreateSubmissionFromProcessResources $dto,
        FormProcessSubmissionCollection $existsProcessSubmissionIds,
        FormIoProcessResource           $processResource,
        FormSubmissionDto               $submission
    ): FormSubmissionDto
    {
        if (!$existsProcessSubmissionIds->hasByForm($processResource->getResource())) {
            $resourceSubmissionData = $this->formIoClient->createSubmission(
                new CreateSubmissionDto(
                    formKey: $processResource->getResource()->getFormKey(),
                    data: FormSubmissionDto::newDraft()
                )
            );
            $this->linkNewSubmissionForProcess($dto->processInstanceId, $processResource->getResource(), $resourceSubmissionData->id);
        } else {
            $submissionId = $existsProcessSubmissionIds->getByForm($processResource->getResource())->getSubmissionId();

            $resourceSubmissionData = $this->formIoClient->getSubmission(
                new GetSubmissionDto(
                    formKey: $processResource->getResource()->getFormKey(),
                    submissionId: $submissionId
                )
            );
        }

        $this->propertyAccessor->setValue(
            $submission->data,
            $processResource->getPath(),
            $resourceSubmissionData
        );

        return $submission;
    }

    private function saveResourceSubmission(
        string                          $processInstanceId,
        FormProcessSubmissionCollection $existsProcessSubmissionIds,
        array                           $data,
        FormIoProcessResource           $processResource): void
    {
        if ($existsProcessSubmissionIds->hasByForm($processResource->getResource())) {
            return;
        }

        $submission = $this->appJsonNormalizer->denormalize(
            data: $this->propertyAccessor->getValue($data, $processResource->getPath()),
            type: FormSubmissionDto::class
        );

        if (!$submission->id) {
            return;
        }

        $this->linkNewSubmissionForProcess($processInstanceId, $processResource->getResource(), $submission->id);
    }

    private function linkNewSubmissionForProcess(string $processInstanceId, FormIo $form, string $submissionId): void
    {
        $newProcessSubmissionId = new FormProcessSubmission();
        $newProcessSubmissionId->setSubmissionId($submissionId);
        $newProcessSubmissionId->setForm($form);
        $newProcessSubmissionId->setProcessInstanceId($processInstanceId);

        $this->processSubmissionRepository->save($newProcessSubmissionId);
    }
}
