<?php declare(strict_types=1);

namespace App\Service\AggregateTaskCompleter;

use App\Service\AggregateTaskProvider\Dto\AggregateTaskDto;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class AggregateTaskTaskCompleter implements AggregateTaskCompleterInterface
{
    /**
     * @param iterable<TaskCompleteAdapterInterface> $taskCompleteAdapters
     */
    public function __construct(
        #[TaggedIterator(TaskCompleteAdapterInterface::class)] private iterable $taskCompleteAdapters
    )
    {
    }

    public function completeTask(CompleteTaskDto $dto): AggregateTaskDto
    {
        foreach ($this->taskCompleteAdapters as $completeAdapter){
            try {
                return $completeAdapter->completeTask($dto);
            } catch (NotFoundHttpException){
                continue;
            }
        }

        throw new NotFoundHttpException('Task not found');
    }
}
