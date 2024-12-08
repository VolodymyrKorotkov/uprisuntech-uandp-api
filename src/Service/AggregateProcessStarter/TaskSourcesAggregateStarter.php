<?php

namespace App\Service\AggregateProcessStarter;

use App\Entity\ApplicationTask;
use App\Entity\ApplicationType;
use App\Enum\ApplicationStrategyEnum;
use App\Repository\ApplicationTypeRepository;
use App\Service\CamundaProcessStarter\CamundaProcessStarter;
use App\Service\FormioGetOrCreateProvider;
use App\Service\NativeProcessStarter\ApplicationTaskStarterInterface;
use App\Service\ProcessSubmissionIdLinker\Dto\LinkSubmissionWithProcessDto;
use App\Service\ProcessSubmissionIdLinker\ProcessSubmissionIdLinker;
use Throwable;

readonly final class TaskSourcesAggregateStarter implements TaskSourcesAggregateStarterInterface
{
    public function __construct(
        private ApplicationTaskStarterInterface $starter,
        private CamundaProcessStarter           $camundaProcessStarter,
        private ApplicationTypeRepository       $applicationTypeRepository,
        private ProcessSubmissionIdLinker       $processSubmissionIdLinker,
        private FormioGetOrCreateProvider       $formioGetOrCreateProvider
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function startAggregateProcess(StartAggregateProcessTaskDto $dto): StartAggregateProcessResultDto
    {
        $appType = $this->applicationTypeRepository->getById($dto->type->id);

        if ($appType->getStrategyType() === ApplicationStrategyEnum::CAMUNDA) {
            $processId = $this->camundaProcessStarter->startProcessInstance($appType->getId());
        } else {
            $processId = $this->startNative($appType, $dto)->getProcessInstanceId();
        }

        if ($dto->submission) {
            $form = $this->formioGetOrCreateProvider->getOrCreateByFormId($dto->submission->form);
            $this->processSubmissionIdLinker->linkSubmissionWithProcess(
                new LinkSubmissionWithProcessDto(
                    processInstanceId: $processId,
                    formIo: $form,
                    submission: $dto->submission
                )
            );
        }

        return new StartAggregateProcessResultDto(
            processId: $processId
        );
    }

    /**
     * @throws Throwable
     */
    private function startNative(ApplicationType $appType, StartAggregateProcessTaskDto $dto): ApplicationTask
    {
        $appTask = new ApplicationTask();
        $appTask->setType($appType);

        $title = $dto->title ?? $appType->getTitle() . ' ' . date(\DateTimeInterface::ATOM);

        return $this->starter->startApplicationTask($appTask, $title);
    }
}
