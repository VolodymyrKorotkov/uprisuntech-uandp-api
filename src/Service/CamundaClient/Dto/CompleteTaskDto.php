<?php

namespace App\Service\CamundaClient\Dto;

final readonly class CompleteTaskDto
{
    public function __construct(
        public string $processInstanceId,
        public string $taskId,
        public array  $variables,
    )
    {
    }
}