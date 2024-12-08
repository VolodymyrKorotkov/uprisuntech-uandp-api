<?php declare(strict_types=1);

namespace App\EntityListener\Organization;

use App\Entity\OrganizationUserRole;
use App\Enum\UserRoleEnum;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

#[AsEntityListener(event: Events::preRemove, method: 'handle', entity: OrganizationUserRole::class)]
final readonly class RemoveSelfOrganizationUserRoleListener
{
    public function __construct(
        private Security $security
    )
    {}

    public function handle(OrganizationUserRole $role): void
    {
        if (!$this->security->getUser()){
            return;
        }

        if ($this->security->isGranted(UserRoleEnum::ROLE_SUPER_ADMIN)){
            return;
        }

        if ($role->getUser()->getUserIdentifier() === $this->security->getUser()->getUserIdentifier()){
            throw new BadRequestException('You can\'t delete yourself');
        }
    }
}
