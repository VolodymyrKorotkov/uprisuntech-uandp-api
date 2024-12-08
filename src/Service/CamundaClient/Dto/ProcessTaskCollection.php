<?php declare(strict_types=1);

namespace App\Service\CamundaClient\Dto;

use Traversable;

final readonly class ProcessTaskCollection implements \IteratorAggregate
{
    /**
     * @param array<CamundaTaskDto> $items
     */
    public function __construct(
        private array $items
    )
    {
    }

    /**
     * @return Traversable<CamundaTaskDto>|CamundaTaskDto[]
     */
    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->items);
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }
}