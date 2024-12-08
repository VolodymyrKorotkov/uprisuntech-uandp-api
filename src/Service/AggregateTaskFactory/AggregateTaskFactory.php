<?php declare(strict_types=1);

namespace App\Service\AggregateTaskFactory;

use App\Entity\ApplicationTask;
use App\Entity\ApplicationType;
use App\Service\AggregateTaskProvider\Dto\AggregateTaskDto;
use App\Service\CamundaClient\Dto\CamundaTaskDto;
use App\Service\CamundaTaskPermission;
use App\Service\FormioGetOrCreateProvider;
use App\Service\FormSubmissionEditLockerService;
use App\Service\NativeTaskCompleter\NativeTaskCompletePermissionChecker;
use App\Service\ProcessSubmissionIdLinker\Dto\GetSubmissionIdForTaskDto;
use App\Service\ProcessSubmissionIdLinker\ProcessSubmissionIdLinker;
use Doctrine\ORM\NonUniqueResultException;
use Throwable;

final readonly class AggregateTaskFactory
{
    public function __construct(
        private FormioGetOrCreateProvider           $formioGetOrCreateProvider,
        private NativeTaskCompletePermissionChecker $nativeTaskCompletePermissionChecker,
        private ProcessSubmissionIdLinker           $processSubmissionIdLinker,
        private CamundaTaskPermission               $camundaTaskPermission,
        private FormSubmissionEditLockerService     $formSubmissionEditLockerService
    )
    {
    }

    /**
     * @throws Throwable
     * @throws NonUniqueResultException
     */
    public function createTaskDtoFromNative(ApplicationTask $appTask): AggregateTaskDto
    {
        $dto = $this->createTaskDtoFromNativeForList($appTask);

        $dto->submissionId = $this->processSubmissionIdLinker->getSubmissionIdForTask(
            GetSubmissionIdForTaskDto::newFromTask($dto)
        );

        $dto->lockForUpdate = $this->isCanNotCompleteTask($appTask) || $this->formSubmissionEditLockerService->isLocked($dto->submissionId);

        return $dto;
    }

    /**
     * @throws NonUniqueResultException
     * @throws Throwable
     */
    public function createTaskFromCamunda(ApplicationType|null $appType, CamundaTaskDto $task): AggregateTaskDto
    {
        $dto = $this->createTaskFromCamundaForList($appType, $task);
        $dto->submissionId = $this->processSubmissionIdLinker->getSubmissionIdForTask(
            GetSubmissionIdForTaskDto::newFromTask($dto)
        );
        $dto->lockForUpdate = $this->camundaTaskPermission->lockForUpdate($task) || $this->formSubmissionEditLockerService->isLocked($dto->submissionId);

        return $dto;
    }

    public function createTaskFromCamundaForList(ApplicationType|null $appType, CamundaTaskDto $task): AggregateTaskDto
    {
        $dto = new AggregateTaskDto();
        $dto->id = $task->id;
        $dto->type = $appType;
        $dto->form = $this->formioGetOrCreateProvider->getOrCreateByFormKey($task->getFormKeyLowerCase());
        $dto->createdAt = new \DateTime($task->created);
        $dto->updatedAt = new \DateTime($task->created);

        $dto->title = trim($appType?->getTitle() . ' "' . $dto->form->getTitle() . '" #' . $task->processInstanceId);
        $dto->processed = false;
        $dto->processId = $task->processInstanceId;

        $dto->lockForUpdate = $this->camundaTaskPermission->lockForUpdate($task);
        $dto->needAssign = $this->camundaTaskPermission->canAssign($task);

        return $dto;
    }

    /**
     * @param ApplicationTask $appTask
     * @return bool
     */
    public function isCanNotCompleteTask(ApplicationTask $appTask): bool
    {
        return !$this->nativeTaskCompletePermissionChecker->canCompleteTask($appTask);
    }

    /**
     * @param ApplicationTask $appTask
     * @return AggregateTaskDto
     */
    public function createTaskDtoFromNativeForList(ApplicationTask $appTask): AggregateTaskDto
    {
        $dto = new AggregateTaskDto();
        $dto->id = $appTask->getTaskId();
        $dto->type = $appTask->getType();
        $dto->form = $appTask->getForm();
        $dto->createdAt = $appTask->getCreatedAt();
        $dto->updatedAt = $appTask->getUpdatedAt();

        $dto->title = $appTask->getTitle();
        $dto->lockForUpdate = $this->isCanNotCompleteTask($appTask);
        $dto->needAssign = $this->nativeTaskCompletePermissionChecker->canAssign($appTask);
        $dto->processed = $appTask->getProcessed();
        $dto->processId = $appTask->getProcessInstanceId();

        return $dto;
    }
}
