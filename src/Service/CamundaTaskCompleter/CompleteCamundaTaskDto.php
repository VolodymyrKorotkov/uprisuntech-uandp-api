<?php declare(strict_types=1);

namespace App\Service\CamundaTaskCompleter;

final readonly class CompleteCamundaTaskDto
{
    public function __construct(
        public string $taskId
    )
    {
    }
}
