<?php declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\IgnoreEntityOwnerViewPermission;
use App\Enum\EntityCrudRoleEnum;
use App\Enum\EntityCrudVoteAttributeEnum;
use App\Security\EntityHasOwnerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class EntityCrudUserIdentityVoter extends Voter
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @param EntityHasOwnerInterface $subject
     * @return string
     */
    public function getOwnerForAllRole(EntityHasOwnerInterface $subject): string
    {
        return EntityCrudRoleEnum::newFromEntityClass($subject::class)->getRoleOwnerForAll()->value;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        $attributeEnum = EntityCrudVoteAttributeEnum::tryFrom($attribute);
        return
            null !== $attributeEnum &&
            ($subject instanceof EntityHasOwnerInterface);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if (!$subject instanceof EntityHasOwnerInterface) {
            throw new \LogicException();
        }

        if ($this->security->isGranted($this->getOwnerForAllRole($subject))) {
            return true;
        }

        if (($attributeEnum = EntityCrudVoteAttributeEnum::tryFrom($attribute)) === EntityCrudVoteAttributeEnum::CREATE_ATTRIBUTE) {
            return true;
        }

        if ($attributeEnum === EntityCrudVoteAttributeEnum::VIEW_ATTRIBUTE && $subject instanceof IgnoreEntityOwnerViewPermission){
            return true;
        }

        return $subject->getUserIdentifier() === $token->getUserIdentifier();
    }
}
