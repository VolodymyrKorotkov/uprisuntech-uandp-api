<?php

namespace App\Security\Voter;

use App\Entity\OrganizationUserRole;
use App\Enum\EntityCrudVoteAttributeEnum;
use App\Enum\UserRoleEnum;
use App\Repository\OrganizationUserRoleRepository;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class OrganizationRoleDeleteVoter extends Voter
{
    public function __construct(
        private readonly OrganizationUserRoleRepository $organizationUserRole,
    )
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return
            $attribute === EntityCrudVoteAttributeEnum::REMOVE_ATTRIBUTE->value &&
            $subject instanceof OrganizationUserRole;
    }

    /**
     * @throws EntityNotFoundException
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if($this->security->isGranted(UserRoleEnum::ROLE_SUPER_ADMIN)){
            return true;
        }
        /** @var OrganizationUserRole $subject */
        return $this->canUserRemoveRole($subject->getId());
    }

    /**
     * @throws EntityNotFoundException
     */
    private function canUserRemoveRole(int $organizationId): bool
    {
        $userRole = $this->organizationUserRole->getUserRole($organizationId, $this->getUserIdentifier());

        return $userRole->getRole() == UserRoleEnum::ROLE_MUNICIPALITY_HEAD;
    }

}
