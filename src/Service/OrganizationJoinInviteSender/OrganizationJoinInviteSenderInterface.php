<?php

namespace App\Service\OrganizationJoinInviteSender;

use App\Entity\OrganizationJoinInvite;

interface OrganizationJoinInviteSenderInterface
{
    public function sendOrganizationJoinInvite(OrganizationJoinInvite $invite): void;
}
