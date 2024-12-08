<?php declare(strict_types=1);

namespace App\Service\KeycloakClient\Dto;

use Doctrine\Common\Collections\ArrayCollection;
use Traversable;

final readonly class KeycloakUserCollection implements \IteratorAggregate
{
    /**
     * @param array<KeycloakUser> $items
     */
    public function __construct(
        private array $items
    ){}

    /**
     * @return Traversable<KeycloakUser>|KeycloakUser[]|array<KeycloakUser>
     */
    public function getIterator(): Traversable
    {
       return new ArrayCollection($this->items);
    }

    public function getFirst(): KeycloakUser
    {
        return $this->items[0];
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

}
