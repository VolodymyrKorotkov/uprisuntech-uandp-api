<?php declare(strict_types=1);

namespace App\Entity;

use App\Repository\SyncUserFromKeycloakRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: SyncUserFromKeycloakRepository::class)]
class SyncUserFromKeycloak
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[Email]
    #[NotBlank]
    #[ORM\Column]
    private ?string $email = null;

    #[ORM\ManyToOne]
    private User|null $user = null;

    #[ORM\Column(type: 'datetime')]
    private DateTimeInterface|null $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function hasUser(): bool
    {
        return null !== $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
