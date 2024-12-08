<?php declare(strict_types=1);

namespace App\EntityListener\OrganizationJoinFlow;

use App\Entity\OrganizationJoinTask;
use App\Enum\OrganizationJoinStatusEnum;
use App\Repository\OrganizationJoinTaskRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsEntityListener(
    event: Events::preUpdate,
    method: 'handle',
    entity: OrganizationJoinTask::class
)]
final readonly class OrganizationJoinTaskStatusValidatorListener
{
    public function __construct(
        private OrganizationJoinTaskRepository $joinTaskRepository
    )
    {}

    public function handle(OrganizationJoinTask $task): void
    {
        if (!$this->isJoinTaskInProgressInDB($task)){
            throw new BadRequestHttpException('You do not can edit task if status is not IN_PROGRESS');
        }
    }

    /**
     * @param OrganizationJoinTask $task
     * @return bool
     */
    private function isJoinTaskInProgressInDB(OrganizationJoinTask $task): bool
    {
        return $this->joinTaskRepository->count([
                'id' => $task->getId(),
                'status' => OrganizationJoinStatusEnum::IN_PROGRESS
            ]) > 0;
    }
}
