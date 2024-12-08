<?php declare(strict_types=1);

namespace App\Service\AggregateTaskProvider\Dto;

use Symfony\Component\Validator\Constraints\NotBlank;

final class GetOneTaskFromAllSourcesDto
{
    #[NotBlank]
    public string|int|null $taskId;

    public function getTaskId(): ?string
    {
        return (string)$this->taskId;
    }

    public function setTaskId(string|int|null $taskId): void
    {
        $this->taskId = $taskId;
    }
}
