<?php

namespace App\EntityListener\OrganizationJoinFlow;

use App\Entity\OrganizationJoinFlow;
use App\Enum\OrganizationJoinStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::preUpdate, method: 'canUserUpdate', entity: OrganizationJoinFlow::class)]
class OrganizationJoinFlowPreUpdateListener
{

    public function canUserUpdate(OrganizationJoinFlow $organizationJoinFlow): void
    {
        if($organizationJoinFlow->getStatus() !== OrganizationJoinStatusEnum::IN_PROGRESS){
           // throw new \Exception('User cannot update the organization join flow');
        }
    }
}