<?php declare(strict_types=1);

namespace App\Service\OrganizationJoinInviteProcessor;

use App\Entity\OrganizationJoinInvite;

final readonly class ProcessOrganizationJoinInviteDto
{
    public function __construct(
        public OrganizationJoinInvite $invite,
        public string                 $userIdentity,
    )
    {}
}
