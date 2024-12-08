<?php declare(strict_types=1);

namespace App\Service\FormioSubmissionSaveHandler\Handler;

use App\Entity\FormIo;
use App\Repository\FormIoRepository;
use App\Service\FormIoClient\Dto\CreateSubmissionDto;
use App\Service\FormIoClient\Dto\EditSubmissionDto;
use App\Service\FormIoClient\Dto\FormSubmission\FormSubmissionDto;
use App\Service\FormIoClient\FormIoClient;
use App\Service\FormioSubmissionSaveHandler\Dto\HandleSubmissionSaveRequestDto;
use App\Service\FormioSubmissionSaveHandler\Dto\HandleSubmissionSaveDto;
use App\Service\KeycloakClient\Dto\KeycloakUser;
use App\Service\KeycloakClient\KeycloakAdminClient;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Throwable;

final readonly class EditProposalHandler implements FormioSubmissionSaveHandlerInterface
{
    const EMAIL_TEMPLATE = 'email/manager_create_proposal.html.twig';

    private PropertyAccessor $propertyAccessor;

    public function __construct(
        private FormIoClient                    $formioClient,
        private KeycloakAdminClient             $keycloakAdminClient,
        private MailerInterface                 $mailer,
        private FormIoRepository                $formIoRepository,
    )
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Throwable
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function handle(HandleSubmissionSaveDto $dto): HandleSubmissionSaveRequestDto
    {
        if (!$dto->formIo->isInstallerProposalForm()) {
            return $dto->formioWebHookRequest;
        }

        $applicationForm = $this->formIoRepository->getApplicationPublicForm();
        $applicationSubmissions = $this->formioClient->getSubmissionList($applicationForm->getFormKey(), [
            $applicationForm->getFilterByApplicationNumberPath() => $this->getApplicationNumber($dto)
        ]);

        if (!count($applicationSubmissions)) {
            throw new NotFoundHttpException('Get Quota submission not found');
        }

        $applicationSubmission = current($applicationSubmissions);

        $user = $this->keycloakAdminClient->getUser($applicationSubmission->owner);

        $proposalSubmission = $dto->formioWebHookRequest->submission;

        if ($dto->formIo->getApplicationResourcePath()) {
            $this->propertyAccessor->setValue(
                objectOrArray: $proposalSubmission->data,
                propertyPath: $dto->formIo->getApplicationResourcePath(),
                value: $this->getApplicationResourceSubmissionFromApplication($applicationSubmission, $applicationForm)
            );
        }

        $proposalSubmission->data['isSaveAction'] = true;
        $proposalSubmission->owner = $user->id;

        if ($proposalSubmission->id) {
            $proposalSubmission = $this->getEditSubmission($user, $dto, $proposalSubmission);
        } else {
            $proposalSubmission = $this->getCreated($user, $dto, $proposalSubmission);
        }

        $this->sendEmailToUser($dto, $proposalSubmission, $user);
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    private function sendEmailToUser(HandleSubmissionSaveDto $dto, FormSubmissionDto $proposalSubmission, KeycloakUser $user): void
    {
        $message = (new TemplatedEmail())
            ->to($user->email)
            ->subject('New application "Get a Quota"')
            ->htmlTemplate(self::EMAIL_TEMPLATE)
            ->context([
                'user' => $user,
                'proposalSubmission' => $proposalSubmission,
                'proposalUrl' => $this->generateProposalUrl($dto->formIo, $proposalSubmission)
            ]);
        $this->mailer->send($message);
    }

    private function getUrl(string $url, array $params): string
    {
        if ($params) {
            $url = str_replace(array_keys($params), array_values($params), $url);
        }

        return $url;
    }

    /**
     * @param FormIo $managerProposalForm
     * @param FormSubmissionDto $proposalSubmission
     * @return string
     */
    private function generateProposalUrl(FormIo $managerProposalForm, FormSubmissionDto $proposalSubmission): string
    {
        return $this->getUrl(
            $managerProposalForm->getSubmissionViewUrl(),
            [
                '{submissionId}' => $proposalSubmission->id,
                '{formId}' => $managerProposalForm->getFormId()
            ]
        );
    }


    /**
     * @param KeycloakUser $user
     * @param HandleSubmissionSaveDto $dto
     * @param FormSubmissionDto|null $proposalSubmission
     * @return FormSubmissionDto
     * @throws Throwable
     */
    private function getEditSubmission(KeycloakUser $user, HandleSubmissionSaveDto $dto, ?FormSubmissionDto $proposalSubmission): FormSubmissionDto
    {
        //$formioUserClient = $this->formioClient->getPrototypeByUserIdentity($user->id);

        return $this->formioClient->editSubmission(
            new EditSubmissionDto(
                formKey: $dto->formIo->getFormKey(),
                submissionId: $proposalSubmission->id,
                data: $proposalSubmission
            )
        );
    }

    /**
     * @param KeycloakUser $user
     * @param HandleSubmissionSaveDto $dto
     * @param FormSubmissionDto|null $proposalSubmission
     * @return FormSubmissionDto
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws Throwable
     */
    private function getCreated(KeycloakUser $user, HandleSubmissionSaveDto $dto, ?FormSubmissionDto $proposalSubmission): FormSubmissionDto
    {
        return $this->formioClient->createSubmission(
            new CreateSubmissionDto(
                formKey: $dto->formIo->getFormKey(),
                data: $proposalSubmission
            )
        );
    }

    /**
     * @param HandleSubmissionSaveDto $dto
     * @return mixed
     */
    private function getApplicationNumber(HandleSubmissionSaveDto $dto): mixed
    {
        return $this->propertyAccessor->getValue(
            objectOrArray: $dto->formioWebHookRequest->submission->data,
            propertyPath: $dto->formIo->getApplicationNumberPropertyPath()
        );
    }

    /**
     * @param FormSubmissionDto $applicationSubmission
     * @param FormIo $applicationForm
     * @return mixed
     */
    private function getApplicationResourceSubmissionFromApplication(FormSubmissionDto $applicationSubmission, FormIo $applicationForm): mixed
    {
        return $this->propertyAccessor->getValue(
            objectOrArray: $applicationSubmission->data,
            propertyPath: $applicationForm->getApplicationResourcePath()
        );
    }
}
