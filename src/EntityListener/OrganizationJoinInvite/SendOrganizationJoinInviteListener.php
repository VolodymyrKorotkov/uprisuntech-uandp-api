<?php declare(strict_types=1);

namespace App\EntityListener\OrganizationJoinInvite;

use App\Entity\OrganizationJoinInvite;
use App\Service\OrganizationJoinInviteSender\OrganizationJoinInviteSenderInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postPersist, method: 'handle', entity: OrganizationJoinInvite::class)]
final readonly class SendOrganizationJoinInviteListener
{
    public function __construct(
        private OrganizationJoinInviteSenderInterface $joinInviteSender
    )
    {}

    public function handle(OrganizationJoinInvite $invite): void
    {
        $this->joinInviteSender->sendOrganizationJoinInvite($invite);
    }
}
