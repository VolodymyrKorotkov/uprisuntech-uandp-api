<?php declare(strict_types=1);

namespace App\Security\Voter;

use App\Enum\EntityCrudVoteAttributeEnum;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

abstract class AbstractEntityViewVoter extends Voter
{
    final protected function supports(string $attribute, mixed $subject): bool
    {
        $class = $this->getEntityClass();

        return $attribute === EntityCrudVoteAttributeEnum::VIEW_ATTRIBUTE->value && $subject instanceof $class;
    }

    abstract protected function getEntityClass(): string;
}
