<?php declare(strict_types=1);

namespace App\Entity\Sub;

use App\Entity\User;
use Traversable;

final readonly class ApplicationUserCollection implements \IteratorAggregate
{
    /**
     * @param array<User> $items
     */
    public function __construct(
        private array $items
    )
    {}

    /**
     * @return Traversable<User>
     */
    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->items);
    }
}