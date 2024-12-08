<?php declare(strict_types=1);

namespace App\Service\AggregateTaskProvider\Dto;

use App\Entity\ApplicationType;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class TasksSourceFilterDto
{
    public function __construct(
        private ApplicationType $applicationType,
        private UserInterface $user,
        private int $offset,
        private int $itemsPerPage
    )
    {
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function getApplicationType(): ApplicationType
    {
        return $this->applicationType;
    }

    public function hasApplicationType(): bool
    {
        return null !== $this->applicationType;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }
}
