<?php declare(strict_types=1);

namespace App\Service\FormIoClient\Dto\FormSubmission;

use Doctrine\Common\Collections\ArrayCollection;
use Traversable;

final readonly class FormSubmissionCollection implements \IteratorAggregate
{
    public function __construct(
        private array $items
    )
    {
    }

    public function getIterator(): Traversable
    {
        return new ArrayCollection($this->items);
    }
}
