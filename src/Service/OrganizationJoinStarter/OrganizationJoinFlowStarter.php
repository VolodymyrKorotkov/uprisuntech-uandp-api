<?php

namespace App\Service\OrganizationJoinStarter;

use App\Entity\OrganizationJoinFlow;
use App\Entity\OrganizationJoinTask;
use App\Enum\UserRoleEnum;
use App\Repository\OrganizationJoinTaskRepository;
use App\Service\KeycloakUserProvider\KeycloakApplicationUserProviderInterface;
use Exception;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;

#[AsAlias(OrganizationJoinFlowStarterInterface::class)]
final readonly class OrganizationJoinFlowStarter implements OrganizationJoinFlowStarterInterface
{
    public function __construct(
        private OrganizationJoinTaskRepository $organizationJoinTaskRepository,
        private KeycloakApplicationUserProviderInterface $keycloakApplicationUserProvider
    )
    {}

    /**
     * @throws Exception
     */
    public function startOrganizationJoinFlow(OrganizationJoinFlow $flow): void
    {
        $users = $this->keycloakApplicationUserProvider->getUsersByRole(
            UserRoleEnum::ROLE_OPERATOR_CASE
        );

        foreach ($users as $user){
            $this->createTask($flow, $user);
        }
    }

    /**
     * @param OrganizationJoinFlow $flow
     * @param $user
     * @return void
     */
    private function createTask(OrganizationJoinFlow $flow, $user): void
    {
        $task = new OrganizationJoinTask();
        $task->setFlow($flow);
        $task->setUser($user);
        $task->setData($flow->getData());

        $this->organizationJoinTaskRepository->save($task);
    }
}
