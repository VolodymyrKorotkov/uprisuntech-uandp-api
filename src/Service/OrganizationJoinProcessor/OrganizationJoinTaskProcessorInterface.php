<?php

namespace App\Service\OrganizationJoinProcessor;

use App\Entity\OrganizationJoinTask;

interface OrganizationJoinTaskProcessorInterface
{
    public function processTask(OrganizationJoinTask $task): void;
}
