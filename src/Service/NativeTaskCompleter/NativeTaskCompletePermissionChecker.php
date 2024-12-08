<?php declare(strict_types=1);

namespace App\Service\NativeTaskCompleter;

use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;
use App\Entity\ApplicationTask;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final readonly class NativeTaskCompletePermissionChecker
{
    public function __construct(
        private Security $security

    )
    {
    }

    public function canCompleteTask(ApplicationTask $applicationTask): bool
    {
        if ($applicationTask->isCompleted()){
            return false;
        }

        if (!$this->security->getUser()){
            return false;
        }

        return $this->security->getUser()->getUserIdentifier() === $applicationTask->getUserIdentifier();
    }

    public function canAssign(ApplicationTask $applicationTask): bool
    {
        if ($applicationTask->hasUser()){
            return false;
        }

        if (!$this->security->getUser()){
            return false;
        }

        return $this->security->isGranted($applicationTask->getRole());
    }
    
    public function throwExceptionIfCanNotEdit(ApplicationTask $applicationTask): void
    {
        if ($applicationTask->isCompleted()){
           throw new BadRequestHttpException('Task is completed');
        }

        if (!$applicationTask->hasUser()){
            throw new AccessDeniedException('Task must have user');
        }

        if ($this->security->getUser()->getUserIdentifier() !== $applicationTask->getUserIdentifier()) {
            throw new AccessDeniedException('You do not have access for task completing');
        }
    }
}
