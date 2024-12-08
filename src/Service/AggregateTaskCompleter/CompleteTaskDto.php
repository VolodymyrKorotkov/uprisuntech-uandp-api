<?php declare(strict_types=1);

namespace App\Service\AggregateTaskCompleter;

use Symfony\Component\Validator\Constraints\NotBlank;

final class CompleteTaskDto
{
    #[NotBlank]
    private string|int|null $id = null;

    public function getId(): string
    {
        return (string)$this->id;
    }

    public function setId(int|string|null $id): void
    {
        $this->id = $id;
    }
}