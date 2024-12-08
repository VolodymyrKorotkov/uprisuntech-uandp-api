<?php declare(strict_types=1);

namespace App\Service\ProcessSubmissionIdLinker\Dto;

use App\Entity\ApplicationTask;
use App\Entity\FormIo;
use App\Service\AggregateTaskProvider\Dto\AggregateTaskDto;
use App\Service\CamundaClient\Dto\CamundaTaskDto;

final readonly class GetSubmissionIdForTaskDto
{
    public function __construct(
        public string      $processInstanceId,
        public FormIo      $formio,
        public null|string $taskId
    )
    {
    }

    public static function newFromTask(AggregateTaskDto $task): GetSubmissionIdForTaskDto
    {
        return new self(
            processInstanceId: $task->processId,
            formio: $task->form,
            taskId: $task->id
        );
    }

    public static function newFromCamundaTask(CamundaTaskDto $taskDto, FormIo $form): GetSubmissionIdForTaskDto
    {
        return new self(
            processInstanceId: $taskDto->processInstanceId,
            formio: $form,
            taskId: $taskDto->id
        );
    }

    public static function newFromNativeTask(ApplicationTask $task): GetSubmissionIdForTaskDto
    {
        return new self(
            processInstanceId: $task->getProcessInstanceId(),
            formio: $task->getForm(),
            taskId: $task->getTaskId()
        );
    }
}
