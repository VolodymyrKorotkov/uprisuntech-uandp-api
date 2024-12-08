<?php declare(strict_types=1);

namespace App\Service\CamundaClient\Dto;

final readonly class TaskListFilterDto
{
    public function __construct(
        public array $params = []
    )
    {
    }
}