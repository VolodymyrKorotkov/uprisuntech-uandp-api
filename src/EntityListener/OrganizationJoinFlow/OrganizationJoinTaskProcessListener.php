<?php

namespace App\EntityListener\OrganizationJoinFlow;

use App\Entity\OrganizationJoinTask;
use App\Enum\OrganizationJoinStatusEnum;
use App\Service\OrganizationJoinProcessor\OrganizationJoinTaskProcessorInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(
    event: Events::postUpdate,
    method: 'handle',
    entity: OrganizationJoinTask::class
)]
final readonly class OrganizationJoinTaskProcessListener
{
    public function __construct(
        private OrganizationJoinTaskProcessorInterface $processor
    )
    {}

    public function handle(OrganizationJoinTask $task): void
    {
        if ($this->flowStatusIsInProgress($task) && !$this->taskStatusIsInProgress($task)) {
            $this->processor->processTask($task);
        }
    }

    /**
     * @param OrganizationJoinTask $task
     * @return bool
     */
    private function flowStatusIsInProgress(OrganizationJoinTask $task): bool
    {
        return $task->getFlow()->getStatus() === OrganizationJoinStatusEnum::IN_PROGRESS;
    }

    /**
     * @param OrganizationJoinTask $task
     * @return bool
     */
    private function taskStatusIsInProgress(OrganizationJoinTask $task): bool
    {
        return $task->getStatus() === OrganizationJoinStatusEnum::IN_PROGRESS;
    }
}
