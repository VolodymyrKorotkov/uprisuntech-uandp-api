<?php declare(strict_types=1);

namespace App\Service\FormioSubmissionSaveHandler\Handler;

use App\Enum\EmailTemplateUseInEnum;
use App\Enum\UserRoleEnum;
use App\Security\ApplicationUserSecurity;
use App\Service\EmailTemplateSender;
use App\Service\FormioSubmissionSaveHandler\Dto\HandleSubmissionSaveDto;
use App\Service\FormioSubmissionSaveHandler\Dto\HandleSubmissionSaveRequestDto;
use App\Service\KeycloakClient\Dto\CreateUserDto;
use App\Service\KeycloakClient\Dto\KeycloakUser;
use App\Service\KeycloakClient\KeycloakAdminClient;
use App\Service\KeycloakUserProvider\KeycloakUniqueUserProviderInterface;
use App\Service\KeycloakUserProvider\KeycloakUserNotFoundException;
use App\Service\KeycloakUserRoleAssigner;
use App\Service\ProcessSubmissionVariable\SubmissionPropertyAccessor;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Throwable;

final readonly class CreateNewUserFromApplicationHandler implements FormioSubmissionSaveHandlerInterface
{
    public function __construct(
        private KeycloakAdminClient                 $keycloakClient,
        private KeycloakUniqueUserProviderInterface $keycloakUserProvider,
        private SubmissionPropertyAccessor          $submissionPropertyAccessor,
        private KeycloakUserRoleAssigner            $keycloakUserRoleAssigner,
        private ApplicationUserSecurity             $applicationUserSecurity,
        private EmailTemplateSender                 $emailTemplateSender
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function handle(HandleSubmissionSaveDto $dto): HandleSubmissionSaveRequestDto
    {
        if (!$dto->formIo->isApplicationPublicForm()) {
            return $dto->formioWebHookRequest;
        }
        if ($dto->formioWebHookRequest->submission->state === 'draft') {
            return $dto->formioWebHookRequest;
        }

        if ($this->applicationUserSecurity->isUserAuth()) {
            $user = $this->keycloakUserProvider->getByIdentity(
                $this->applicationUserSecurity->getUser()->getUserIdentifier()
            );
        } else {
            $user = $this->getByEmailFromKeycloak($dto);
        }

        $result = $dto->formioWebHookRequest;
        $result->submission->owner = $user->id;

        return $result;
    }

    /**
     * @throws Throwable
     */
    private function getByEmailFromKeycloak(HandleSubmissionSaveDto $dto): KeycloakUser
    {
        try {
            $user = $this->keycloakUserProvider->getByEmail($this->getApplicationEmail($dto));
        } catch (KeycloakUserNotFoundException) {
            $user = $this->createUser($dto);
        }

        if ($user->emailVerified) {
            throw new BadRequestException('Verified user already exists with this email');
        }
        return $user;
    }

    /**
     * @throws Throwable
     */
    private function createUser(HandleSubmissionSaveDto $dto): KeycloakUser
    {
        $email = $this->getApplicationEmail($dto);
        $username = $email;
        $password = uniqid();
        $this->keycloakClient->createUser(
            $createDto = new CreateUserDto(
                email: $email,
                password: $password,
                username: $username,
                firstName: $this->getApplicationFirstName($dto),
                lastName: $this->getApplicationLastName($dto),
            )
        );

        $user = $this->keycloakUserProvider->getByUsername($username);
        $this->keycloakUserRoleAssigner->assignRole($user->id, UserRoleEnum::ROLE_USER_CASE->value);

        $this->sendEmailToNewUser($createDto, $user);

        return $user;
    }

    /**
     * @throws Throwable
     */
    private function sendEmailToNewUser(CreateUserDto $createDto, KeycloakUser $user): void
    {
        $this->emailTemplateSender->sendEmail(
            $user->email,
            useIn: EmailTemplateUseInEnum::CREATE_USER,
            context: [
                'password' => $createDto->password,
                'user' => $user,
                'loginUrl' => 'https://stage.uprisun.dev/'
            ]
        );
    }

    /**
     * @param HandleSubmissionSaveDto $dto
     * @return string
     * @throws Throwable
     */
    private function getApplicationEmail(HandleSubmissionSaveDto $dto): string
    {
        return $this->submissionPropertyAccessor->getApplicationEmail(
            submission: $dto->formioWebHookRequest->submission,
            formIo: $dto->formIo
        );
    }

    /**
     * @param HandleSubmissionSaveDto $dto
     * @return mixed
     */
    private function getApplicationFirstName(HandleSubmissionSaveDto $dto): string
    {
        return $this->submissionPropertyAccessor->getApplicationFirstName(
            submission: $dto->formioWebHookRequest->submission,
            formIo: $dto->formIo
        );
    }

    /**
     * @param HandleSubmissionSaveDto $dto
     * @return mixed
     */
    private function getApplicationLastName(HandleSubmissionSaveDto $dto): string
    {
        return $this->submissionPropertyAccessor->getApplicationLastName(
            submission: $dto->formioWebHookRequest->submission,
            formIo: $dto->formIo
        );
    }
}
