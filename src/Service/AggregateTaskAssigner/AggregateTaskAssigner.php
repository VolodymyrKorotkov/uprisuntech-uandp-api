<?php declare(strict_types=1);

namespace App\Service\AggregateTaskAssigner;

use App\Repository\ApplicationTask\NativeTaskRepository;
use App\Security\ApplicationUserSecurity;
use App\Service\AggregateTaskFactory\AggregateTaskFactory;
use App\Service\AggregateTaskProvider\Dto\AggregateTaskDto;
use App\Service\CamundaTaskAssigner\CamundaTaskAuthUserAssigner;
use App\Service\NativeTaskProvider\NativeTaskAuthUserProvider;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

final readonly class AggregateTaskAssigner
{
    public function __construct(
        private CamundaTaskAuthUserAssigner $authUserAssigner,
        private AggregateTaskFactory $aggregateTaskFactory,
        private NativeTaskAuthUserProvider $nativeTaskAuthUserProvider,
        private ApplicationUserSecurity $security,
        private NativeTaskRepository $nativeTaskRepository
    )
    {}

    /**
     * @throws Throwable
     * @throws NonUniqueResultException
     */
    public function assignTask(AssignTaskDto $dto): AggregateTaskDto
    {
        try {
            return $this->assignNativeTask($dto);
        } catch (NotFoundHttpException){
            return $this->assignCamunda($dto);
        }
    }

    private function assignNativeTask(AssignTaskDto $dto): AggregateTaskDto
    {
        $task = $this->nativeTaskAuthUserProvider->getNativeTask($dto->getId());
        $task->setUserIdentifier(
            $this->security->getUser()->getUserIdentifier()
        );
        $task->setRole(null);

        $this->nativeTaskRepository->save($task);

        return $this->aggregateTaskFactory->createTaskDtoFromNative($task);
    }

    /**
     * @param AssignTaskDto $dto
     * @return AggregateTaskDto
     * @throws NonUniqueResultException
     * @throws Throwable
     */
    private function assignCamunda(AssignTaskDto $dto): AggregateTaskDto
    {
        return $this->aggregateTaskFactory->createTaskFromCamunda(
            appType: null,
            task: $this->authUserAssigner->assignForAuthUser($dto->getId())
        );
    }
}
