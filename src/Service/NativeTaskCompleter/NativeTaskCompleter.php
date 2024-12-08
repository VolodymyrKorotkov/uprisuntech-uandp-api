<?php

namespace App\Service\NativeTaskCompleter;

use App\Entity\ApplicationTask;
use App\Repository\ApplicationTask\NativeTaskRepository;
use Exception;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Throwable;

#[AsAlias]
final readonly class NativeTaskCompleter implements NativeTaskCompleterInterface
{
    public function __construct(
        private NativeTaskCompletePermissionChecker $completePermissionChecker,
        private NativeTaskRepository                $applicationTaskRepository
    )
    {
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    public function completeTask(ApplicationTask $applicationTask): void
    {
        $this->completePermissionChecker->throwExceptionIfCanNotEdit($applicationTask);

        $applicationTask = $this->completeAndUpdateTask($applicationTask);

        $this->createNextTasks($applicationTask);
    }

    /**
     * @throws Throwable
     */
    private function completeAndUpdateTask(ApplicationTask $applicationTask): ApplicationTask
    {
        if ($applicationTask->getType()->getNativeStrategy()->getNeverComplete()){
            return $applicationTask;
        }

        $applicationTask->setCompleted(true);
        $this->applicationTaskRepository->save($applicationTask);

        return $applicationTask;
    }

    /**
     * @param ApplicationTask $parentTask
     * @return void
     * @throws Throwable
     */
    public function createNextTasks(ApplicationTask $parentTask): void
    {
        $parentStrategy = $parentTask->getType()->getNativeStrategy();
        if ($parentStrategy->nextIsEmpty()) {
            return;
        } else {
            $nextType = $parentStrategy->getNextType();
        }

        $applicationTaskNew = new ApplicationTask();
        $applicationTaskNew
            ->setTaskId(UuidV4::uuid4()->toString())
            ->setProcessInstanceId($parentTask->getProcessInstanceId());
        $applicationTaskNew->setForm(
            $parentStrategy->getNextType()->getNativeStrategy()->getForm()
        );

        $applicationTaskNew->setType($nextType);
        $applicationTaskNew->setParentTask($parentTask);

        $applicationTaskNew->setRole(
            $parentStrategy->getNextType()->getRole()
        );

        $this->applicationTaskRepository->save($applicationTaskNew);
    }
}
