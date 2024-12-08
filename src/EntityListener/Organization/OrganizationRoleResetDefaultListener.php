<?php declare(strict_types=1);

namespace App\EntityListener\Organization;

use App\Entity\OrganizationUserRole;
use App\Repository\OrganizationUserRoleRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postUpdate, method: 'handleEvent', entity: OrganizationUserRole::class)]
#[AsEntityListener(event: Events::postPersist, method: 'handleEvent', entity: OrganizationUserRole::class)]
final readonly class OrganizationRoleResetDefaultListener
{
    public function __construct(
        private OrganizationUserRoleRepository $roleRepository
    )
    {}

    public function handleEvent(OrganizationUserRole $orgRole): void
    {
        if (!$orgRole->isDefault()){
            return;
        }

        $editRoles = [];
        foreach ($this->roleRepository->getAllDefaultRoles($orgRole->getUser()->getUserIdentifier()) as $existsRole){
            if ($existsRole === $orgRole){
                continue;
            }
            $editRoles[] = $existsRole->setDefault(false);
        }

        if ($editRoles) {
            $this->roleRepository->saveMultiple($editRoles);
        }
    }
}
