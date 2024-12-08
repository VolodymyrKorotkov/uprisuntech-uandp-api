<?php declare(strict_types=1);

namespace App\Service\OrganizationJoinProcessor;

use App\Entity\Organization;
use App\Entity\OrganizationJoinTask;
use App\Entity\OrganizationUserRole;
use App\Enum\OrganizationJoinStatusEnum;
use App\Enum\UserRoleEnum;
use App\Repository\OrganizationRepository;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;

#[AsAlias(OrganizationJoinTaskProcessorInterface::class)]
final readonly class OrganizationJoinTaskProcessor implements OrganizationJoinTaskProcessorInterface
{
    public function __construct(
        private OrganizationRepository $organizationRepository
    )
    {}

    public function processTask(OrganizationJoinTask $task): void
    {
        if ($task->getStatus() !== OrganizationJoinStatusEnum::CONFIRMED) {
            return;
        }

        try {
            $organization = $this->organizationRepository->getByEdrpou($task->getData()->getEdrpou());
        } catch (EntityNotFoundException){
            $organization = new Organization();
            $organization->setUser($task->getFlow()->getUser());
            $organization->setEdrpou($task->getData()->getEdrpou());
        }

        $organization->setTitle($task->getData()->getTitle());

        $organization->addRole(
            $this->newOrganizationRoleEntity($task)
        );

        $this->organizationRepository->save($organization);
    }

    /**
     * @param OrganizationJoinTask $task
     * @return OrganizationUserRole
     */
    private function newOrganizationRoleEntity(OrganizationJoinTask $task): OrganizationUserRole
    {
        $organizationRole = new OrganizationUserRole();
        $organizationRole->setRole(
            $task->getData()->getRole() ?? UserRoleEnum::ROLE_MUNICIPALITY_HEAD_CASE
        );
        $organizationRole->setUser(
            $task->getFlow()->getUser()
        );
        $organizationRole->setDefault(true);

        return $organizationRole;
    }
}
