<?php

namespace App\Service\CamundaClient;

use App\Service\CamundaClient\Dto\CamundaTaskDto;
use App\Service\CamundaClient\Dto\CompleteTaskDto;
use App\Service\CamundaClient\Dto\CountTasksResultDto;
use App\Service\CamundaClient\Dto\ProcessTaskCollection;
use App\Service\CamundaClient\Dto\StartProcessDto;
use App\Service\CamundaClient\Dto\TaskListFilterDto;

interface CamundaClientInterface
{

    public function startProcessAndGetId(StartProcessDto $dto): string;
    public function getProcessTask(string $processInstanceId): CamundaTaskDto;
    public function getTasks(TaskListFilterDto $filterDto): ProcessTaskCollection;
    public function countTasks(TaskListFilterDto $filterDto): CountTasksResultDto;
    public function getTask(string $taskId): CamundaTaskDto;
    public function updateTask(CamundaTaskDto $taskDto): void;
    public function getTaskFormKey(string $taskId): ?string;
    public function completeTask(CompleteTaskDto $completeDto): void;
}
