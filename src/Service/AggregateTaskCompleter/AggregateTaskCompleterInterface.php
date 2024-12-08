<?php

namespace App\Service\AggregateTaskCompleter;

use App\Service\AggregateTaskProvider\Dto\AggregateTaskDto;

interface AggregateTaskCompleterInterface
{
    public function completeTask(CompleteTaskDto $dto): AggregateTaskDto;
}
