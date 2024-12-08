<?php declare(strict_types=1);

namespace App\Repository\FormProcessSubmissionVariable;

use App\Entity\FormProcessSubmissionVariable;
use Traversable;

final class FormProcessSubmissionVariableCollection implements \IteratorAggregate
{
    /**
     * @var FormProcessSubmissionVariable[]
     */
    private array $items = [];

    /**
     * @param array<FormProcessSubmissionVariable> $items
     */
    public function __construct(
        array $items
    )
    {
        foreach ($items as $item){
            $this->items[$item->getKey()] = $item;
        }
    }

    public function hasVarByKey(string $key): bool
    {
        return isset($this->items[$key]);
    }

    public function getVarByKey(string $key): FormProcessSubmissionVariable
    {
        return $this->items[$key];
    }

    /**
     * @return Traversable<FormProcessSubmissionVariable>|FormProcessSubmissionVariable[]
     */
    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->items);
    }

    public function getSubmissionIds(): array
    {
        $submissionIds = [];
        foreach ($this->items as $item){
            $submissionIds[] = $item->getSubmissionId();
        }

        return array_unique($submissionIds);
    }
}
