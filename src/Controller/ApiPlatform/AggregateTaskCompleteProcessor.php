<?php declare(strict_types=1);

namespace App\Controller\ApiPlatform;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Service\AggregateTaskCompleter\AggregateTaskCompleterInterface;

final readonly class AggregateTaskCompleteProcessor implements ProcessorInterface
{
    public function __construct(
        private AggregateTaskCompleterInterface $aggregateTaskCompleter
    )
    {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        return $this->aggregateTaskCompleter->completeTask($data);
    }
}
