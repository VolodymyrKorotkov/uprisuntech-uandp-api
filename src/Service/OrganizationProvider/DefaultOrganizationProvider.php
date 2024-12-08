<?php declare(strict_types=1);

namespace App\Service\OrganizationProvider;

use App\Entity\Organization;
use App\Entity\OrganizationUserRole;
use App\Repository\OrganizationUserRoleRepository;
use App\Service\DefaultOrganizationProviderInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class DefaultOrganizationProvider implements DefaultOrganizationProviderInterface
{
    public function __construct(
        private OrganizationUserRoleRepository $roleRepository
    )
    {}

    /**
     * @throws NotFoundHttpException
     * @throws NonUniqueResultException
     */
    public function getDefaultOrganization(string $userIdentity): Organization
    {
        return $this->getDefaultOrganizationRole($userIdentity)->getOrganization();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getDefaultOrganizationRole(string $userIdentity): OrganizationUserRole
    {
        try {
            return $this->roleRepository->getOneDefaultRole(
                $userIdentity
            );
        } catch (EntityNotFoundException){
            throw new NotFoundHttpException('Default organization not found');
        }
    }
}
