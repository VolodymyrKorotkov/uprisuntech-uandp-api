<?php declare(strict_types=1);

namespace App\Service\OrganizationJoinProcessor;

use App\Entity\OrganizationJoinTask;
use App\Enum\OrganizationJoinStatusEnum;
use App\Repository\OrganizationJoinFlowRepository;
use App\Repository\OrganizationJoinTaskRepository;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator(OrganizationJoinTaskProcessorInterface::class)]
final readonly class UpdateFlowAndTaskStatusDecorator implements OrganizationJoinTaskProcessorInterface
{
    public function __construct(
        private OrganizationJoinTaskProcessorInterface $processor,
        private OrganizationJoinFlowRepository $joinFlowRepository,
        private OrganizationJoinTaskRepository $joinTaskRepository
    )
    {}

    public function processTask(OrganizationJoinTask $task): void
    {
        $this->updateFlowStatus($task);
        $this->updateTasksStatus($task);

        $this->processor->processTask($task);
    }

    private function updateFlowStatus(OrganizationJoinTask $task): void
    {
        $task->getFlow()->setStatus(
            $task->getStatus()
        );
        $this->joinFlowRepository->save($task->getFlow());
    }

    /**
     * @param OrganizationJoinTask $task
     * @return void
     */
    private function updateTasksStatus(OrganizationJoinTask $task): void
    {
        $tasks = [];
        foreach ($this->getOrganizationJoinTasksByStatus($task) as $otherTask) {
            $otherTask->setStatus($task->getStatus());
            $tasks[] = $otherTask;
        }
        $this->joinTaskRepository->saveMultiple($tasks);
    }

    /**
     * @param OrganizationJoinTask $task
     * @return OrganizationJoinTask[]
     */
    private function getOrganizationJoinTasksByStatus(OrganizationJoinTask $task): array
    {
        return $this->joinTaskRepository->getListByStatus(
            flowId: $task->getFlow()->getId(),
            status: OrganizationJoinStatusEnum::IN_PROGRESS
        );
    }
}
