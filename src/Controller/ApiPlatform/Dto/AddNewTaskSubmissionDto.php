<?php declare(strict_types=1);

namespace App\Controller\ApiPlatform\Dto;

use Symfony\Component\Validator\Constraints\NotBlank;

final class AddNewTaskSubmissionDto
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
