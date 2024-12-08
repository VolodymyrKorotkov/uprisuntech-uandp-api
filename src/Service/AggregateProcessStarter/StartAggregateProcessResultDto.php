<?php declare(strict_types=1);

namespace App\Service\AggregateProcessStarter;

final readonly class StartAggregateProcessResultDto
{
    public function __construct(
        public string $processId
    )
    {

    }
}
