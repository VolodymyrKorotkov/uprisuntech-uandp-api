<?php

namespace App\Service;

use App\Entity\Organization;
use App\Entity\OrganizationUserRole;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

interface DefaultOrganizationProviderInterface
{
    /**
     * @throws NotFoundHttpException
     */
    public function getDefaultOrganization(string $userIdentity): Organization;

    /**
     * @throws NotFoundHttpException
     */
    public function getDefaultOrganizationRole(string $userIdentity): OrganizationUserRole;
}
