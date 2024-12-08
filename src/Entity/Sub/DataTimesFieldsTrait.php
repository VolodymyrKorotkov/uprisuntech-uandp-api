<?php

namespace App\Entity\Sub;

use Doctrine\ORM\Mapping as ORM;

trait DataTimesFieldsTrait
{
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface|null $updatedAt;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface|null $createdAt;

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PreUpdate]
    #[ORM\PrePersist]
    public function setUpdatedAtNow(): static
    {
        $this->updatedAt = new \DateTime();

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtNow(): static
    {
        $this->createdAt = new \DateTime();

        return $this;
    }
}
