<?php

namespace App\Service\CamundaTaskProvider;

use App\Service\CamundaClient\Dto\CamundaTaskDto;
use App\Service\CamundaClient\Dto\ProcessTaskCollection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

interface CamundaTaskByFilterProviderInterface
{
    public function getTasks(GetTaskListFilterDto $filterDto): ProcessTaskCollection;

    /**
     * @throws NotFoundHttpException
     */
    public function getTask(string $taskId): CamundaTaskDto;
    public function countTasks(GetTaskListFilterDto $filterDto): int;
}
