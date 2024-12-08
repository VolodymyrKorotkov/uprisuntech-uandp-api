<?php declare(strict_types=1);

namespace App\EntityListener\OrganizationJoinFlow;

use App\Entity\OrganizationJoinFlow;
use App\EventListener\AbstractEntityPermissionListener;
use App\Security\ApplicationUserSecurity;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: OrganizationJoinFlow::class, priority: AbstractEntityPermissionListener::EVENT_PRIORITY-1)]
final readonly class OrganizationJoinFlowSetUserListener
{
    public const AFTER_USER_SET = 998;
    public function __construct(private ApplicationUserSecurity $applicationUserSecurity)
    {}

    public function prePersist(OrganizationJoinFlow $organizationJoinFlow): void
    {
        if (!$this->applicationUserSecurity->isUserAuth()){
            return;
        }

        $organizationJoinFlow->setUser(
            $this->applicationUserSecurity->getUser()
        );
    }
}
