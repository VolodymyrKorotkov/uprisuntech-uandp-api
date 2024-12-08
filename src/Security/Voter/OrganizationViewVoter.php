<?php declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Organization;
use App\Enum\EntityCrudVoteAttributeEnum;
use App\Repository\OrganizationUserRoleRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class OrganizationViewVoter extends Voter
{
    public function __construct(
        private readonly Security $security,
        private readonly OrganizationUserRoleRepository $organizationUserRoleRepository
    )
    {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return
            $attribute === EntityCrudVoteAttributeEnum::VIEW_ATTRIBUTE->value &&
            $subject instanceof Organization;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return $this->doVoteOnAttribute($subject);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    private function doVoteOnAttribute(Organization $entity): bool
    {
        if ($this->security->getUser()->getUserIdentifier() === $entity->getUserIdentifier()){
            return true;
        }

        return $this->organizationUserRoleRepository->hasUserRole(
            organizationId: $entity->getId(),
            userIdentity: $this->security->getUser()->getUserIdentifier()
        );
    }
}
