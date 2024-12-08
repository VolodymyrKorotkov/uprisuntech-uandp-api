<?php declare(strict_types=1);

namespace App\EntityListener\Organization;

use App\Entity\Organization;
use App\Repository\OrganizationRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

#[AsEntityListener(event: Events::postPersist, method: 'handleEvent', entity: Organization::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'handleEvent', entity: Organization::class)]
final readonly class OrganizationResetDefaultListener
{
    private OrganizationRepository $organizationRepository;

    public function __construct(OrganizationRepository $organizationRepository)
    {
        $this->organizationRepository = $organizationRepository;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function handleEvent(Organization $org): void
    {
        if (!$org->isDefault()){
            return;
        }

        try {
            $default = $this->organizationRepository->getOtherDefaultOrganization($org->getId());
        } catch (NoResultException){
            return;
        }

        if ($default === $org){
            return;
        }

        $default->setDefault(false);
        $this->organizationRepository->save($default);
    }
}
