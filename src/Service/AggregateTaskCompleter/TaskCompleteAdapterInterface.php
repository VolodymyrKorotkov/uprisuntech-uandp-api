<?php

namespace App\Service\AggregateTaskCompleter;

use App\Service\AggregateTaskProvider\Dto\AggregateTaskDto;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AutoconfigureTag(TaskCompleteAdapterInterface::class)]
interface TaskCompleteAdapterInterface
{
    /**
     * @throws NotFoundHttpException
     */
    public function completeTask(CompleteTaskDto $dto): AggregateTaskDto;
}
