<?php

namespace App\Service\AggregateTaskProvider;

use App\Service\AggregateTaskProvider\Dto\GetOneTaskFromAllSourcesDto;
use App\Service\AggregateTaskProvider\Dto\GetOneTskFromAllSourcesResult;
use App\Service\AggregateTaskProvider\Dto\ProcessTasksCollection;
use App\Service\AggregateTaskProvider\Dto\TasksFromAllSourcesFilterDto;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

interface TaskSourcesAggregateProviderInterface
{
    public function getTasksFromAllSources(TasksFromAllSourcesFilterDto $dto): ProcessTasksCollection;

    /**
     * @param GetOneTaskFromAllSourcesDto $dto
     * @return GetOneTskFromAllSourcesResult
     * @throws NotFoundHttpException
     */
    public function getOneTaskFromAllSources(GetOneTaskFromAllSourcesDto $dto): GetOneTskFromAllSourcesResult;
}
