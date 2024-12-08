<?php declare(strict_types=1);

namespace App\Service\FormioSubmissionSaveHandler\Handler;

use App\Enum\EmailTemplateUseInEnum;
use App\Repository\FormIoRepository;
use App\Service\EmailTemplateSender;
use App\Service\FormIoClient\FormIoClient;
use App\Service\FormioSubmissionSaveHandler\Dto\HandleSubmissionSaveDto;
use App\Service\FormioSubmissionSaveHandler\Dto\HandleSubmissionSaveRequestDto;
use App\Service\FormioSubmissionSaveHandler\SubmissionStatusChecker;
use App\Service\ProcessSubmissionVariable\PropertyValueIsEmptyException;
use App\Service\ProcessSubmissionVariable\SubmissionPropertyAccessor;
use Doctrine\ORM\EntityNotFoundException;

final readonly class SendUserEmailAfterEditManagerQuotaHandler implements FormioSubmissionSaveHandlerInterface
{
    public function __construct(
        private FormIoRepository                     $formIoRepository,
        private SubmissionPropertyAccessor           $submissionPropertyAccessor,
        private EmailTemplateSender                  $emailTemplateSender,
        private SubmissionStatusChecker $submissionStatusChecker,
        private FormIoClient                         $formIoClient,
    )
    {
    }

    /**
     * @throws PropertyValueIsEmptyException
     * @throws \Throwable
     * @throws EntityNotFoundException
     */
    public function handle(HandleSubmissionSaveDto $dto): HandleSubmissionSaveRequestDto
    {
        if (!$dto->formIo->isManagerProposalForm()){
            return $dto->formioWebHookRequest;
        }

        if (!$this->submissionStatusChecker->isConfirmedStatus($dto->formioWebHookRequest->submission, $dto->formIo)){
            return $dto->formioWebHookRequest;
        }

        $appForm = $this->formIoRepository->getApplicationPublicForm();
        $applicationNumber = $this->getApplicationNumber($dto);
        $appSubmission = $this->formIoClient->getSubmissionList(
           $appForm->getFormKey(),
            [
                $appForm->getFilterByApplicationNumberPath() => $applicationNumber
            ]
        )[0] ?? null;

        $email = $this->submissionPropertyAccessor->getApplicationEmail(
            $appSubmission,
            $appForm
        );

        $this->emailTemplateSender->sendEmail(
            $email,
            useIn: EmailTemplateUseInEnum::HAVE_NEW_QUOTA,
            context: [
                'quotaSubmission' => $dto->formioWebHookRequest->submission,
                'applicationSubmission' => $appSubmission,
                'applicationNumber' => $applicationNumber
            ]
        );

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
            submission: $dto->formioWebHookRequest->submission,
            formIo: $dto->formIo
        );
    }
}
