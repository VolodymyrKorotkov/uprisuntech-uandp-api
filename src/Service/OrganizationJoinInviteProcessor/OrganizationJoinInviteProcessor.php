<?php declare(strict_types=1);

namespace App\Service\OrganizationJoinInviteProcessor;

use App\Entity\OrganizationUserRole;
use App\Enum\OrganizationJoinInviteStatusEnum;
use App\Enum\UserRoleEnum;
use App\Repository\OrganizationJoinInviteRepository;
use App\Repository\OrganizationUserRoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;

#[AsAlias(OrganizationJoinInviteProcessorInterface::class)]
final readonly class OrganizationJoinInviteProcessor implements OrganizationJoinInviteProcessorInterface
{
    public function __construct(
        private OrganizationJoinInviteRepository $organizationJoinInviteRepository,
        private UserRepository $applicationUserRepository,
        private OrganizationUserRoleRepository $organizationUserRoleRepository,
        private Security $security
    )
    {}

    /**
     * @throws EntityNotFoundException
     */
    public function processOrganizationJoinInvite(ProcessOrganizationJoinInviteDto $dto): OrganizationUserRole
    {
        $invite = $dto->invite;

        $invite->setStatus(OrganizationJoinInviteStatusEnum::CONFIRMED);
        $invite->setComment('Success!!!');
        $invite->setFullName($this->getUserFullName());
        $this->organizationJoinInviteRepository->save($invite);

        return $this->createOrganizationRole($dto);
    }

    /**
     * @throws EntityNotFoundException
     */
    private function createOrganizationRole(ProcessOrganizationJoinInviteDto $dto): OrganizationUserRole
    {
        $role = new OrganizationUserRole();
        $role->setUser(
            $this->applicationUserRepository->getByUserIdentity($dto->userIdentity)
        )
            ->setOrganization($dto->invite->getOrganization())
            ->setDefault(true)
            ->setRole(UserRoleEnum::ROLE_MUNICIPALITY_MANAGER_CASE);

        $this->organizationUserRoleRepository->save($role);

        return $role;
    }

    /**
     * @throws EntityNotFoundException
     */
    private function getUserFullName(): string
    {
        $user = $this->applicationUserRepository->getByUserIdentity($this->security->getUser()->getUserIdentifier());
        return $user->getName().' '.$user->getLastname(). ' '.$user->getMiddleName();
    }
}
