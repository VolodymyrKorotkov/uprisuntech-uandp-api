<?php declare(strict_types=1);

namespace App\Controller\ApiPlatform;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Service\AggregateTaskAssigner\AggregateTaskAssigner;
use App\Service\AggregateTaskAssigner\AssignTaskDto;
use App\Service\AggregateTaskProvider\Dto\AggregateTaskDto;

final readonly class AggregateTaskAssignerProcessor implements ProcessorInterface
{
    public function __construct(
        private AggregateTaskAssigner $aggregateTaskAssigner
    )
    {}

    /**
     * @param AssignTaskDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): AggregateTaskDto
    {
        return $this->aggregateTaskAssigner->assignTask($data);
    }
}
