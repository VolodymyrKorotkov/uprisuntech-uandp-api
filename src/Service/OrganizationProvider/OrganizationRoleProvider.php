<?php

namespace App\Service\OrganizationProvider;

use App\Entity\Organization;
use App\Entity\OrganizationUserRole;
use App\Repository\OrganizationUserRoleRepository;
use Doctrine\ORM\EntityNotFoundException;

final readonly class OrganizationRoleProvider implements OrganizationRoleProviderInterface
{
    public function __construct(
        private OrganizationUserRoleRepository $organizationUserRoleRepository,
    )
    {}

    /**
     * @throws UserRoleNotFoundException
     */
    public function getUserIdentityRecursiveUp(Organization $organization, string $role): OrganizationUserRole
    {
        try {
            return $this->organizationUserRoleRepository->getUserIdentity(organizationId: $organization->getId(), role: $role);
        } catch (EntityNotFoundException $exception){
            if (!$organization->getParent()){
                throw new UserRoleNotFoundException(previous: $exception);
            }

            return $this->getUserIdentityRecursiveUp(organization: $organization->getParent(), role: $role);
        }
    }

    /**
     * @throws UserRoleNotFoundException
     */
    public function getUserRoleRecursiveUp(Organization $organization, string $userIdentity): OrganizationUserRole
    {
        try {
            return $this->organizationUserRoleRepository->getUserRole(organizationId: $organization->getId(), userIdentity: $userIdentity);
        } catch (EntityNotFoundException $exception){
            if (!$organization->getParent()){
                throw new UserRoleNotFoundException(previous: $exception);
            }

            return $this->getUserRoleRecursiveUp(organization: $organization->getParent(), userIdentity: $userIdentity);
        }
    }

    public function hasUserRoleRecursiveDown(Organization $organization, string $userIdentity): bool
    {
        if ($this->hasUserRole($organization, $userIdentity)){
            return true;
        }

        foreach ($organization->getChildren() as $childOrganization){
            if ($this->hasUserRole($childOrganization, $userIdentity)){
                return true;
            }
        }

        return true;
    }

    /**
     * @param Organization $organization
     * @param string $userIdentity
     * @return bool
     */
    private function hasUserRole(Organization $organization, string $userIdentity): bool
    {
        return $this->organizationUserRoleRepository->hasUserRole(
            organizationId: $organization->getId(),
            userIdentity: $userIdentity
        );
    }

    public function hasUserRoleRecursiveUp(Organization $organization, string $userIdentity): bool
    {
        $hasRole = $this->organizationUserRoleRepository->hasUserRole(organizationId: $organization->getId(), userIdentity: $userIdentity);

        if (false === $hasRole && $organization->getParent()){
            return $this->hasUserRoleRecursiveDown($organization->getParent(), $userIdentity);
        }

        return $hasRole;
    }
}
