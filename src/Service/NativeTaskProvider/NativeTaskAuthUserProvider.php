<?php declare(strict_types=1);

namespace App\Service\NativeTaskProvider;

use App\Entity\ApplicationTask;
use App\Repository\ApplicationTask\NativeTaskRepository;

final readonly class NativeTaskAuthUserProvider
{
    public function __construct(
        private NativeTaskRepository $nativeTaskRepository
    )
    {
    }

    public function getNativeTask(string $id): ApplicationTask
    {
        return $this->nativeTaskRepository->getByTaskId($id);
    }
}
