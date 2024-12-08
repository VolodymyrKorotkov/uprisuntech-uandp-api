<?php declare(strict_types=1);

namespace App\Service;

use App\Service\CamundaClient\Dto\CamundaTaskDto;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class CamundaTaskPermission
{
    public function __construct(
        private Security $security
    )
    {
    }

    public function canAssign(CamundaTaskDto $task): bool
    {
        return $task->assignee !== $this->security->getUser()->getUserIdentifier();
    }

    public function canComplete(CamundaTaskDto $task): bool
    {
        return $task->assignee === $this->security->getUser()->getUserIdentifier();
    }

    public function lockForUpdate(CamundaTaskDto $task): bool
    {
        return false === $this->canComplete($task);
    }
}