<?php

namespace App\Service\AggregateProcessStarter;

interface TaskSourcesAggregateStarterInterface
{
    public function startAggregateProcess(StartAggregateProcessTaskDto $dto): StartAggregateProcessResultDto;
}
