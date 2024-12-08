<?php declare(strict_types=1);

namespace App\Service\CamundaTaskProvider;

final readonly class GetTaskListFilterDto
{
    public function __construct(
        public int $typeId
    )
    {
    }
}