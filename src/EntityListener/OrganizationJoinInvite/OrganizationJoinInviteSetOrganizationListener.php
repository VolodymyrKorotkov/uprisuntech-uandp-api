<?php declare(strict_types=1);

namespace App\EntityListener\OrganizationJoinInvite;

use App\Entity\Organization;
use App\Entity\OrganizationJoinInvite;
use App\Security\ApplicationUserSecurity;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsEntityListener(event: Events::prePersist, method: 'handle', entity: OrganizationJoinInvite::class)]
final readonly class OrganizationJoinInviteSetOrganizationListener
{
    public function __construct(
        private ApplicationUserSecurity $security
    )
    {}

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function handle(OrganizationJoinInvite $invite): void
    {
        if (!$this->security->isUserAuth()){
            return;
        }

        if (!$invite->hasOrganization()) {
            $invite->setOrganization(
                $this->getDefaultOrganizationAsOwner()
            );
        }
    }

    /**
     * @return Organization
     */
    private function getDefaultOrganizationAsOwner(): Organization
    {
        try {
            return $this->security->getDefaultOrganizationAsOwner();
        } catch (\Throwable){
            throw new BadRequestHttpException('You do not have organization');
        }
    }
}
