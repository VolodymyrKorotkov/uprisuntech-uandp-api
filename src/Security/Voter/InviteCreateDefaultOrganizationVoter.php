<?php declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\OrganizationJoinInvite;
use App\Enum\EntityCrudVoteAttributeEnum;
use App\Repository\OrganizationRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class InviteCreateDefaultOrganizationVoter extends Voter
{
    public function __construct(
        private readonly Security $security,
        private readonly OrganizationRepository $organizationRepository
    )
    {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return
                $attribute === EntityCrudVoteAttributeEnum::CREATE_ATTRIBUTE->value &&
                $subject instanceof OrganizationJoinInvite;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return $this->organizationRepository->hasDefaultOrganizationAsMunicipalityHead(
            $this->security->getUser()->getUserIdentifier()
        );
    }
}
