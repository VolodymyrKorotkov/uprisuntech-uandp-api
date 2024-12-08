<?php declare(strict_types=1);

namespace App\Service\AggregateTaskProvider\Dto;

final class TasksFromAllSourcesFilterDto
{
    private string|int|null $typeId = null;
    private int|string|null $page = 1;
    private int|string|null $itemsPerPage = 10;

    public function getTypeId(): int
    {
        return (int)$this->typeId;
    }

    public function setTypeId(int|string|null $typeId): void
    {
        $this->typeId = $typeId;
    }

    public function hasTypeId(): bool
    {
        return $this->typeId !== null;
    }

    public function getPage(): int
    {
        return (int)($this->page ?? 1);
    }

    public function setPage(int|string|null $page): void
    {
        $this->page = $page;
    }

    public function getItemsPerPage(): int
    {
        return (int)($this->itemsPerPage ?? 10);
    }

    public function setItemsPerPage(int|string|null $itemsPerPage): void
    {
        $this->itemsPerPage = $itemsPerPage;
    }

    public function getOffset(): int
    {
        return $this->getPage() * $this->getItemsPerPage() - $this->getItemsPerPage();
    }
}
