<?php declare(strict_types=1);

namespace App\Service\OrganizationProvider;

use App\Entity\Organization;
use App\Entity\OrganizationUserRole;

interface OrganizationRoleProviderInterface
{
    /**
     * @throws UserRoleNotFoundException
     */
    public function getUserIdentityRecursiveUp(Organization $organization, string $role): OrganizationUserRole;

    /**
     * @throws UserRoleNotFoundException
     */
    public function getUserRoleRecursiveUp(Organization $organization, string $userIdentity): OrganizationUserRole;
    public function hasUserRoleRecursiveUp(Organization $organization, string $userIdentity): bool;
    public function hasUserRoleRecursiveDown(Organization $organization, string $userIdentity): bool;
}
