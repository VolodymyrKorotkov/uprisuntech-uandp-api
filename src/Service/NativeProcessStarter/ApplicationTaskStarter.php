<?php declare(strict_types=1);

namespace App\Service\NativeProcessStarter;

use App\Entity\ApplicationProcess;
use App\Entity\ApplicationTask;
use App\Entity\NativeStrategy;
use App\Repository\ApplicationProcessRepository;
use App\Repository\ApplicationTask\NativeTaskRepository;
use App\Security\ApplicationUserSecurity;
use Exception;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Throwable;

#[AsAlias]
final readonly class ApplicationTaskStarter implements ApplicationTaskStarterInterface
{
    public function __construct(
        private NativeTaskRepository         $applicationTaskRepository,
        private ApplicationProcessRepository $processRepository,
        private ApplicationUserSecurity      $applicationUserSecurity
    )
    {
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    public function startApplicationTask(ApplicationTask $task, string $processTitle): ApplicationTask
    {
        $strategy = $task->getType()->getNativeStrategy();
        if (!$strategy->isAllowStartProcess()) {
            throw new BadRequestHttpException();
        }

        $this->processRepository->save(
            $processInstance = $this->getProcessEntity($task, $processTitle)
        );

        $this->applicationTaskRepository->save(
            $task = $this->createTaskEntity($task, $strategy, $processInstance)
        );

        return $task;
    }

    /**
     * @param ApplicationTask $task
     * @param NativeStrategy|null $strategy
     * @param ApplicationProcess $process
     * @return ApplicationTask
     */
    private function createTaskEntity(ApplicationTask $task, ?NativeStrategy $strategy, ApplicationProcess $process): ApplicationTask
    {
        $task
            ->setTaskId(UuidV4::uuid4()->toString())
            ->setProcessInstanceId($process->getProcessInstanceId());
        $task->setForm(
            $strategy->getForm()
        );
        $task->setRole($strategy->getRole());
        $task->setTitleFromProcessInstance($process);

        return $task;
    }

    private function getProcessEntity(ApplicationTask $task, string $processTitle): ApplicationProcess
    {
        $processEntity = new ApplicationProcess();
        $processEntity->setProcessInstanceId(UuidV4::uuid4()->toString());
        $processEntity->setType($task->getType());
        $processEntity->setTitle($processTitle);

        if ($this->applicationUserSecurity->isUserAuth()){
            $processEntity->setUser($this->applicationUserSecurity->getUser());
        }

        return $processEntity;
    }
}
