<?php declare(strict_types=1);

namespace App\Service\AggregateTaskCompleter;

use App\Service\AggregateTaskFactory\AggregateTaskFactory;
use App\Service\AggregateTaskProvider\Dto\AggregateTaskDto;
use App\Service\NativeTaskCompleter\NativeTaskCompleterInterface;
use App\Service\NativeTaskProvider\NativeTaskAuthUserProvider;

final readonly class NativeTaskSourceTaskCompleteAdapter implements TaskCompleteAdapterInterface
{
    public function __construct(
        private NativeTaskCompleterInterface $nativeTaskCompleter,
        private NativeTaskAuthUserProvider $nativeTaskRepository,
        private AggregateTaskFactory $aggregateTaskFactory
    )
    {
    }

    public function completeTask(CompleteTaskDto $dto): AggregateTaskDto
    {
        $appTask = $this->nativeTaskRepository->getNativeTask($dto->getId());
        $this->nativeTaskCompleter->completeTask($appTask);

        return $this->aggregateTaskFactory->createTaskDtoFromNative($appTask);
    }
}
