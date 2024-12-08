<?php

namespace App\Enum;

use App\Entity\ApplicationTask;
use App\Entity\CamundaStrategy;
use App\Entity\CamundaTaskFilter;
use App\Entity\FormIo;
use App\Entity\FormProcessSubmission;
use App\Entity\NativeStrategy;
use App\Entity\Organization;
use App\Entity\OrganizationJoinFlow;
use App\Entity\OrganizationJoinInvite;
use App\Entity\OrganizationJoinTask;
use App\Entity\User;

enum ResourcePermissionKeyEnum: string
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
}
