<?php

namespace App\Service\OrganizationJoinInviteProcessor;

use App\Entity\OrganizationUserRole;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

interface OrganizationJoinInviteProcessorInterface
{
    public const SET_ERROR_PRIORITY = 0;
    public const VALIDATE_DRFO_CODE_PRIORITY = 1;

    public const VALIDATE_EXISTS_ORGANIZATION = 2;
    /**
     * @throws BadRequestHttpException
     */
    public function processOrganizationJoinInvite(ProcessOrganizationJoinInviteDto $dto): OrganizationUserRole;
}
