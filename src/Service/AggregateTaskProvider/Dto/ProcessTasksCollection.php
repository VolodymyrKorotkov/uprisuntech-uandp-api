<?php declare(strict_types=1);

namespace App\Service\AggregateTaskProvider\Dto;

use App\Serializer\SerializerGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Traversable;

final class ProcessTasksCollection
{
    /**
     * @var AggregateTaskDto[]
     */
    private array|null $tasksAsArray = null;
    public function __construct(
        private readonly \Closure $tasks,
        private readonly int      $count
    )
    {}

    /**
     * @return Traversable<AggregateTaskDto>|AggregateTaskDto[]
     */
    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->getTasksAsArray());
    }

    /**
     * @return AggregateTaskDto[]
     */
    #[Groups(SerializerGroupsEnum::SAFE_VIEW)]
    #[SerializedName('hydra:member')]
    public function getTasksAsArray(): array
    {
        if (null === $this->tasksAsArray){
            $this->tasksAsArray = ($this->tasks)();
        }

        return $this->tasksAsArray;
    }

    #[Groups(SerializerGroupsEnum::SAFE_VIEW)]
    #[SerializedName('hydra:totalItems')]
    public function getCount(): int
    {
        return $this->count;
    }
}
