<?php declare(strict_types=1);

namespace App\Service\AggregateTaskProvider\Dto;

use App\Enum\ApplicationStrategyEnum;

final readonly class GetOneTskFromAllSourcesResult
{
    public function __construct(
        public AggregateTaskDto        $task,
        public ApplicationStrategyEnum $strategyType
    )
    {
    }
}