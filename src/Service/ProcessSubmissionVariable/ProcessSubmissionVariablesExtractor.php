<?php declare(strict_types=1);

namespace App\Service\ProcessSubmissionVariable;

use App\Entity\FormProcessSubmission;
use App\Entity\FormProcessSubmissionVariable;
use App\Repository\FormProcessSubmissionVariable\FormProcessSubmissionVariableRepository;
use App\Serializer\AppJsonNormalizerCopyInterface;
use App\Service\FormIoClient\Dto\FormMetadata\FormComponentsDto;
use App\Service\FormIoClient\Dto\GetFormDto;
use App\Service\FormIoClient\FormIoClient;
use Throwable;

final readonly class ProcessSubmissionVariablesExtractor
{
    public function __construct(
        private FormIoClient                            $formIoClient,
        private AppJsonNormalizerCopyInterface          $appJsonNormalizer,
        private FormProcessSubmissionVariableRepository $processSubmissionVariableRepository
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function extractVarsFromFormComponentProperties(FormProcessSubmission $dto): void
    {
        $vars = $this->processSubmissionVariableRepository->findForProcess($dto->getSubmissionId());

        foreach ($this->getFormComponents($dto) as $component){
            if ($component->propertyVarKeyIsEmpty()){
                continue;
            }

            if ($vars->hasVarByKey($component->getPropertyVarKey())){
                continue;
            }

            $this->processSubmissionVariableRepository->persist(
                $this->createVarEntity($component, $dto)
            );
        }

        $this->processSubmissionVariableRepository->flush();
    }

    /**
     * @return array<FormComponentsDto>
     * @throws Throwable
     */
    private function getFormComponents(FormProcessSubmission $dto): array
    {
        $form = $this->formIoClient->getForm(
            new GetFormDto($dto->getForm()->getFormKey())
        );

        return $this->appJsonNormalizer->denormalize(
            data: $form->components,
            type: FormComponentsDto::class . '[]'
        );
    }

    private function createVarEntity(FormComponentsDto $component, FormProcessSubmission $dto): FormProcessSubmissionVariable
    {
        $newVar = new FormProcessSubmissionVariable();
        $newVar->setKey($component->properties->varKey);
        $newVar->setSubmissionId($dto->getSubmissionId());
        $newVar->setProcessInstanceId($dto->getProcessInstanceId());
        $newVar->setSubmissionProperty($component->key);

        return $newVar;
    }
}
