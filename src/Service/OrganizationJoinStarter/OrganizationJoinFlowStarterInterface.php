<?php

namespace App\Service\OrganizationJoinStarter;

use App\Entity\OrganizationJoinFlow;

interface OrganizationJoinFlowStarterInterface
{
    public function startOrganizationJoinFlow(OrganizationJoinFlow $flow): void;
}
