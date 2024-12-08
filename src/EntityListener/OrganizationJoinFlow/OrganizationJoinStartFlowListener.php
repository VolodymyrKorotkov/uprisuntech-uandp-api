<?php

namespace App\EntityListener\OrganizationJoinFlow;

use App\Entity\OrganizationJoinFlow;
use App\Service\OrganizationJoinStarter\OrganizationJoinFlowStarterInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(
    event: Events::postPersist,
    method: 'handle',
    entity: OrganizationJoinFlow::class,
    priority: OrganizationJoinFlowSetUserListener::AFTER_USER_SET
)]
final readonly class OrganizationJoinStartFlowListener
{
    public function __construct(
        private OrganizationJoinFlowStarterInterface $formFlowStarter
    ){}

    public function handle(OrganizationJoinFlow $organizationJoinFlow): void
    {
        $this->formFlowStarter->startOrganizationJoinFlow($organizationJoinFlow);
    }
}
