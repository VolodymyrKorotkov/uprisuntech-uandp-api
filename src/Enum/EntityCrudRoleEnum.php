<?php

namespace App\Enum;

use App\Entity\ApplicationProcess;
use App\Entity\ApplicationTask;
use App\Entity\CamundaStrategy;
use App\Entity\CamundaTaskFilter;
use App\Entity\EmailTemplate;
use App\Entity\FormIo;
use App\Entity\FormProcessSubmission;
use App\Entity\FormProcessSubmissionVariable;
use App\Entity\FormSubmissionEditLocker;
use App\Entity\FormTaskSubmission;
use App\Entity\InstallerEmail;
use App\Entity\NativeStrategy;
use App\Entity\Organization;
use App\Entity\OrganizationJoinFlow;
use App\Entity\OrganizationJoinInvite;
use App\Entity\OrganizationJoinTask;
use App\Entity\User;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

enum EntityCrudRoleEnum: string
{
    case FORMIO = FormIo::class;
    case APPLICATION_TASK = ApplicationTask::class;
    case ORGANIZATION = Organization::class;
    case CAMUNDA_STRATEGY = CamundaStrategy::class;
    case NATIVE_STRATEGY = NativeStrategy::class;
    case CAMUNDA_PROCESS_FILTER = CamundaTaskFilter::class;
    case ORGANIZATION_JOIN_FLOW = OrganizationJoinFlow::class;
    case ORGANIZATION_JOIN_TASK = OrganizationJoinTask::class;
    case ORGANIZATION_JOIN_INVITE = OrganizationJoinInvite::class;
    case USER = User::class;
    case FORM_PROCESS_SUBMISSIONS = FormProcessSubmission::class;
    case FORM_PROCESS_SUBMISSIONS_VARIABLE = FormProcessSubmissionVariable::class;
    case FORM_PROCESS_TASK_SUBMISSION = FormTaskSubmission::class;
    case APP_PROCESS = ApplicationProcess::class;
    case INSTALLER = InstallerEmail::class;
    case EMAIL_TEMPLATE = EmailTemplate::class;
    case SUBMISSION_EDIT_LOCKER = FormSubmissionEditLocker::class;

    public static function newFromEntityClass(string $entityClass): EntityCrudRoleEnum
    {
        return
            self::tryFrom($entityClass) ??
            throw new AccessDeniedException(self::class.': Not found roles for entity '.$entityClass);
    }

    public function getDefaultRole(): UserRoleEnum
    {
        return match ($this){
          self::FORMIO => UserRoleEnum::ROLE_FORMIO,
          self::CAMUNDA_STRATEGY => UserRoleEnum::ROLE_CAMUNDA_STRATEGY,
          self::NATIVE_STRATEGY => UserRoleEnum::ROLE_NATIVE_STRATEGY,
          self::ORGANIZATION_JOIN_FLOW => UserRoleEnum::ROLE_ORGANIZATION_JOIN_FLOW,
          self::ORGANIZATION_JOIN_TASK => UserRoleEnum::ROLE_ORGANIZATION_JOIN_TASK,
          self::ORGANIZATION_JOIN_INVITE => UserRoleEnum::ROLE_ORGANIZATION_JOIN_INVITE,
          default => UserRoleEnum::ROLE_SUPER_ADMIN_CASE
        };
    }

    public function getRoleView(): UserRoleEnum
    {
        return match ($this){
            self::FORMIO => UserRoleEnum::ROLE_FORMIO_VIEW,
            self::ORGANIZATION => UserRoleEnum::ROLE_ORGANIZATION,
            self::APPLICATION_TASK => UserRoleEnum::ROLE_APPLICATION_TASK,
            self::CAMUNDA_STRATEGY => UserRoleEnum::ROLE_CAMUNDA_STRATEGY,
            self::NATIVE_STRATEGY => UserRoleEnum::ROLE_NATIVE_STRATEGY,
            self::ORGANIZATION_JOIN_FLOW => UserRoleEnum::ROLE_ORGANIZATION_JOIN_FLOW,
            self::ORGANIZATION_JOIN_TASK => UserRoleEnum::ROLE_ORGANIZATION_JOIN_TASK,
            self::ORGANIZATION_JOIN_INVITE => UserRoleEnum::ROLE_ORGANIZATION_JOIN_INVITE,
            default => UserRoleEnum::ROLE_SUPER_ADMIN_CASE
        };
    }

    public function getRoleEdit(): UserRoleEnum
    {
        return match ($this){
            self::FORMIO => UserRoleEnum::ROLE_FORMIO_EDIT,
            self::APPLICATION_TASK => UserRoleEnum::ROLE_APPLICATION_TASK,
            self::CAMUNDA_STRATEGY => UserRoleEnum::ROLE_CAMUNDA_STRATEGY,
            self::NATIVE_STRATEGY => UserRoleEnum::ROLE_NATIVE_STRATEGY,
            self::ORGANIZATION_JOIN_FLOW => UserRoleEnum::ROLE_ORGANIZATION_JOIN_FLOW,
            self::ORGANIZATION_JOIN_TASK => UserRoleEnum::ROLE_ORGANIZATION_JOIN_TASK,
            self::ORGANIZATION_JOIN_INVITE => UserRoleEnum::ROLE_ORGANIZATION_JOIN_INVITE,
            default => UserRoleEnum::ROLE_SUPER_ADMIN_CASE
        };
    }

    public function getRoleCreate(): UserRoleEnum
    {
        return match ($this){
            self::FORMIO => UserRoleEnum::ROLE_FORMIO_CREATE,
            self::APPLICATION_TASK => UserRoleEnum::ROLE_APPLICATION_TASK,
            self::CAMUNDA_STRATEGY => UserRoleEnum::ROLE_CAMUNDA_STRATEGY,
            self::NATIVE_STRATEGY => UserRoleEnum::ROLE_NATIVE_STRATEGY,
            self::ORGANIZATION_JOIN_FLOW => UserRoleEnum::ROLE_ORGANIZATION_JOIN_FLOW,
            self::ORGANIZATION_JOIN_TASK => UserRoleEnum::ROLE_ORGANIZATION_JOIN_TASK,
            self::ORGANIZATION_JOIN_INVITE => UserRoleEnum::ROLE_ORGANIZATION_JOIN_INVITE,
            default => UserRoleEnum::ROLE_SUPER_ADMIN_CASE
        };
    }

    public function getRoleDelete(): UserRoleEnum
    {
        return match ($this){
            self::FORMIO => UserRoleEnum::ROLE_FORMIO_DELETE,
            self::CAMUNDA_STRATEGY => UserRoleEnum::ROLE_CAMUNDA_STRATEGY,
            self::NATIVE_STRATEGY => UserRoleEnum::ROLE_NATIVE_STRATEGY,
            self::ORGANIZATION_JOIN_FLOW => UserRoleEnum::ROLE_ORGANIZATION_JOIN_FLOW,
            self::ORGANIZATION_JOIN_TASK => UserRoleEnum::ROLE_ORGANIZATION_JOIN_TASK,
            self::ORGANIZATION_JOIN_INVITE => UserRoleEnum::ROLE_ORGANIZATION_JOIN_INVITE,
            default => UserRoleEnum::ROLE_SUPER_ADMIN_CASE
        };
    }

    public function getRoleOwnerForAll(): UserRoleEnum
    {
        return match ($this){
            self::FORMIO => UserRoleEnum::ROLE_FORMIO_DELETE,
            self::NATIVE_STRATEGY => UserRoleEnum::ROLE_NATIVE_STRATEGY,
            self::APPLICATION_TASK => UserRoleEnum::ROLE_APPLICATION_TASK_OWNER_FOR_ALL,
            self::ORGANIZATION_JOIN_FLOW => UserRoleEnum::ROLE_ORGANIZATION_JOIN_FLOW,
            self::ORGANIZATION_JOIN_TASK => UserRoleEnum::ROLE_ORGANIZATION_JOIN_TASK,
            self::ORGANIZATION_JOIN_INVITE => UserRoleEnum::ROLE_ORGANIZATION_JOIN_INVITE,
            default => UserRoleEnum::ROLE_SUPER_ADMIN_CASE
        };
    }
}
