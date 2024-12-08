<?php declare(strict_types=1);

namespace App\Service\FormioSubmissionSaveHandler\Handler;

use App\Entity\ApplicationType;
use App\Entity\FormIo;
use App\Entity\InstallerEmail;
use App\Entity\InstallerQuotaSubmissionId;
use App\Enum\EmailTemplateUseInEnum;
use App\Repository\ApplicationTypeRepository;
use App\Repository\FormIoRepository;
use App\Repository\InstallerEmailRepository;
use App\Service\AggregateProcessStarter\StartAggregateProcessTaskDto;
use App\Service\AggregateProcessStarter\TaskSourcesAggregateStarterInterface;
use App\Service\EmailTemplateSender;
use App\Service\FormIoClient\Dto\FormSubmission\FormSubmissionDto;
use App\Service\FormioSubmissionSaveHandler\Dto\HandleSubmissionSaveDto;
use App\Service\FormioSubmissionSaveHandler\Dto\HandleSubmissionSaveRequestDto;
use App\Service\ProcessSubmissionIdLinker\Dto\GetSubmissionIdForProcessDto;
use App\Service\ProcessSubmissionIdLinker\ProcessSubmissionIdLinker;
use App\Service\ProcessSubmissionVariable\PropertyValueIsEmptyException;
use App\Service\ProcessSubmissionVariable\SubmissionPropertyAccessor;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Throwable as ThrowableAlias;

final readonly class StartApplicationProposalProcessHandler implements FormioSubmissionSaveHandlerInterface
{
    const INSTALLERS_PROPOSALS_TYPE_ALIAS = 'installers_proposals';

    public function __construct(
        private FormIoRepository                     $formIoRepository,
        private SubmissionPropertyAccessor           $submissionPropertyAccessor,
        private TaskSourcesAggregateStarterInterface $processStarter,
        private ApplicationTypeRepository            $applicationTypeRepository,
        private InstallerEmailRepository             $installerEmailRepository,
        private EmailTemplateSender                  $emailTemplateSender,
        private ProcessSubmissionIdLinker            $processSubmissionIdLinker,
        private EntityManagerInterface               $entityManager
    )
    {
    }

    /**
     * @throws ThrowableAlias
     */
    public function handle(HandleSubmissionSaveDto $dto): HandleSubmissionSaveRequestDto
    {
        if (!$dto->formIo->isApplicationPublicForm()) {
            return $dto->formioWebHookRequest;
        }
        if ($dto->formioWebHookRequest->submission->state === 'draft') {
            return $dto->formioWebHookRequest;
        }

        $installerProposalForm = $this->formIoRepository->getInstallerProposalForm();
        $appType = $this->applicationTypeRepository->getByAlias(self::INSTALLERS_PROPOSALS_TYPE_ALIAS);

        foreach ($this->findInstallers($dto) as $installer) {
            $this->startApplicationProcess($dto, $appType, $installer, $installerProposalForm);
        }

        return $dto->formioWebHookRequest;
    }

    /**
     * @throws ThrowableAlias
     * @throws NonUniqueResultException
     */
    private function startApplicationProcess(
        HandleSubmissionSaveDto $handleSubmissionSaveDto,
        ApplicationType         $applicationType,
        InstallerEmail          $installer,
        FormIo                  $installerForm
    ): void
    {
        $startProcessDto = new StartAggregateProcessTaskDto();
        $startProcessDto->setTypeId($applicationType->getId());
        $startProcessDto->title = $this->getApplicationNumber($handleSubmissionSaveDto) . ' "' . $installer->getTitle() . '"';
        $startProcessDto->submission = $handleSubmissionSaveDto->formioWebHookRequest->submission;

        $processStartResult = $this->processStarter->startAggregateProcess($startProcessDto);

        $installerProposalSubmission = $this->processSubmissionIdLinker->getSubmissionForProcess(
            new GetSubmissionIdForProcessDto(
                processInstanceId: $processStartResult->processId,
                formio: $installerForm
            )
        );

        $installerSubmissionId = new InstallerQuotaSubmissionId();
        $installerSubmissionId->setInstallerEmail($installer);
        $installerSubmissionId->setSubmissionId($installerProposalSubmission->id);
        $this->entityManager->persist($installerSubmissionId);
        $this->entityManager->flush();


        $this->emailTemplateSender->sendEmail(
            $installer->getEmail(),
            useIn: EmailTemplateUseInEnum::INSTALLER_SEND_MESSAGE,
            context: $this->getEmailContext($handleSubmissionSaveDto, $installer, $installerProposalSubmission, $installerForm)
        );
    }

    private function getUrl(string $url, array $params): string
    {
        if ($params) {
            $url = str_replace(array_keys($params), array_values($params), $url);
        }

        return $url;
    }

    /**
     * @param HandleSubmissionSaveDto $dto
     * @return string
     * @throws PropertyValueIsEmptyException
     */
    private function getApplicationNumber(HandleSubmissionSaveDto $dto): string
    {
        return $this->submissionPropertyAccessor->getApplicationNumber(
            submission: $dto->formioWebHookRequest->submission,
            formIo: $dto->formIo
        );
    }

    /**
     * @param HandleSubmissionSaveDto $handleSubmissionSaveDto
     * @param InstallerEmail $installer
     * @param FormSubmissionDto $installerProposalSubmission
     * @param FormIo $installerForm
     * @return array
     * @throws PropertyValueIsEmptyException
     */
    private function getEmailContext(HandleSubmissionSaveDto $handleSubmissionSaveDto, InstallerEmail $installer, FormSubmissionDto $installerProposalSubmission, FormIo $installerForm): array
    {
        return [
            'applicationMainData' => [
                'applicationNumber' => $this->getApplicationNumber($handleSubmissionSaveDto)
            ],
            'proposalUrl' => $this->getUrl(
                $installer->getCallBackUrl(),
                [
                    '{submissionId}' => $installerProposalSubmission->id,
                    '{formId}' => $installerForm->getFormId(),
                    '{formKey}' => $installerForm->getFormKey(),
                ]
            ),
            'applicationSubmission' => $handleSubmissionSaveDto->formioWebHookRequest->submission,
            'proposalSubmission' => $installerProposalSubmission
        ];
    }

    public function findInstallers(HandleSubmissionSaveDto $dto): array
    {
        $stateShort = $this->getStateShort($dto);
        $zipCode = $this->getZipCode($dto);

        if (!$stateShort && !$zipCode){
            throw new BadRequestException('Address and zipcode is empty');
        }

        return $this->installerEmailRepository->findAllEnabled($stateShort, $zipCode);
    }

    private function getStateShort(HandleSubmissionSaveDto $dto): ?string
    {
        try {
            $addressData = $this->submissionPropertyAccessor->getAddress(
                $dto->formioWebHookRequest->submission,
                $dto->formIo
            );

            foreach ($addressData['address_components'] ?? [] as $addressComponent) {
                if ($addressComponent['types'][0] === 'administrative_area_level_1') {
                    return $addressComponent['short_name'];
                }
            }
        } catch (PropertyValueIsEmptyException) {
            return null;
        }

        return null;
    }

    private function getZipCode(HandleSubmissionSaveDto $dto): ?int
    {
        try {
            return (int)$this->submissionPropertyAccessor->getZipCode(
                $dto->formioWebHookRequest->submission,
                $dto->formIo
            );
        } catch (PropertyValueIsEmptyException) {
            return null;
        }
    }
}
