<?php declare(strict_types=1);

namespace App\Service\FormioSubmissionSaveHandler\Handler;

use App\Entity\FormIo;
use App\Entity\InstallerEmail;
use App\Enum\EmailTemplateUseInEnum;
use App\Repository\FormIoRepository;
use App\Repository\InstallerQuotaSubmissionIdRepository;
use App\Service\EmailTemplateSender;
use App\Service\FormIoClient\Dto\EditSubmissionDto;
use App\Service\FormIoClient\Dto\FormSubmission\FormSubmissionDto;
use App\Service\FormIoClient\FormIoClient;
use App\Service\FormioSubmissionSaveHandler\Dto\HandleSubmissionSaveDto;
use App\Service\FormioSubmissionSaveHandler\Dto\HandleSubmissionSaveRequestDto;
use App\Service\FormSubmissionEditLockerService;
use App\Service\ProcessSubmissionVariable\PropertyValueIsEmptyException;
use App\Service\ProcessSubmissionVariable\SubmissionPropertyAccessor;
use Doctrine\ORM\EntityNotFoundException;
use Throwable;

final readonly class UnlockQuotaEditSubmissionAfterApplicationEditHandler implements FormioSubmissionSaveHandlerInterface
{
    public function __construct(
        private FormIoRepository                     $formIoRepository,
        private SubmissionPropertyAccessor           $submissionPropertyAccessor,
        private FormIoClient                         $formIoClient,
        private FormSubmissionEditLockerService      $submissionEditLocker,
        private InstallerQuotaSubmissionIdRepository $installerQuotaSubmissionIdRepository,
        private EmailTemplateSender                  $emailTemplateSender
    )
    {
    }

    /**
     * @throws PropertyValueIsEmptyException
     * @throws Throwable
     * @throws EntityNotFoundException
     */
    public function handle(HandleSubmissionSaveDto $dto): HandleSubmissionSaveRequestDto
    {
        if (!$dto->formIo->isApplicationPublicForm()) {
            return $dto->formioWebHookRequest;
        }

        $this->unlockSubmission(
            $this->formIoRepository->getManagerProposalForm(), false, $dto);
        $this->unlockSubmission(
            $this->formIoRepository->getInstallerProposalForm(), true, $dto);

        return $dto->formioWebHookRequest;
    }

    /**
     * @param HandleSubmissionSaveDto $dto
     * @return string
     * @throws PropertyValueIsEmptyException
     */
    private function getApplicationNumber(HandleSubmissionSaveDto $dto): string
    {
        return $this->submissionPropertyAccessor->getApplicationNumber(
            $dto->formioWebHookRequest->submission,
            $dto->formIo
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

    private function getUrl(string $url, array $params): string
    {
        if ($params) {
            $url = str_replace(array_keys($params), array_values($params), $url);
        }

        return $url;
    }

    /**
     * @throws PropertyValueIsEmptyException
     * @throws Throwable
     */
    private function unlockSubmission(FormIo $formIo, bool $isInstaller, HandleSubmissionSaveDto $dto): void
    {
        $appNumber = $this->getApplicationNumber($dto);
        $submissions = $this->formIoClient->getSubmissionList(
            formKey: $formIo->getFormKey(),
            filter: [
                $formIo->getFilterByApplicationNumberPath() => $appNumber
            ]
        );

        foreach ($submissions as $submission) {
            $this->submissionEditLocker->unlockSubmissionForEdit($submission->id);
            $submission = $this->submissionPropertyAccessor->setStatusValue(
                $submission,
                $formIo,
                $this->submissionPropertyAccessor->getStatusDraftValue($formIo)
            );

            $this->formIoClient->editSubmission(
                new EditSubmissionDto(
                    formKey: $formIo->getFormKey(),
                    submissionId: $submission->id,
                    data: $submission
                )
            );

            if ($isInstaller){
                $this->sendInstallerEmail($submission, $dto, $formIo);
            } else {
                $this->sendManagerEmail($submission, $dto, $formIo);
            }
        }
    }

    /**
     * @param FormSubmissionDto $submission
     * @param HandleSubmissionSaveDto $dto
     * @param FormIo $formIo
     * @return void
     * @throws PropertyValueIsEmptyException
     */
    private function sendInstallerEmail(FormSubmissionDto $submission, HandleSubmissionSaveDto $dto, FormIo $formIo): void
    {
        $installerSubmissionId = $this->installerQuotaSubmissionIdRepository->findOneBy([
            'submissionId' => $submission->id
        ]);

        if (!$installerSubmissionId) {
            return;
        }

        $this->emailTemplateSender->sendEmail(
            $installerSubmissionId->getInstallerEmail()->getEmail(),
            useIn: EmailTemplateUseInEnum::INSTALLER_SEND_MESSAGE,
            context: $this->getEmailContext($dto, $installerSubmissionId->getInstallerEmail(), $submission, $formIo)
        );
    }

    private function sendManagerEmail(FormSubmissionDto $submission, HandleSubmissionSaveDto $dto, FormIo $formIo)
    {
        //
    }
}
