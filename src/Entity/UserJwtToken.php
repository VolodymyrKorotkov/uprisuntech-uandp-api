<?php declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserJwtTokenRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserJwtTokenRepository::class)]
class UserJwtToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: 'text', length: 255, nullable: true)]
    private ?string $jwtToken = null;

    #[ORM\Column(type: 'text', length: 255, nullable: true)]
    private ?string $refreshToken = null;

    #[ORM\ManyToOne]
    private User|null $user = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface|null $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function __toString(): string
    {
        return (string) $this->user.' '.$this->createdAt->format(\DateTimeInterface::ATOM);
    }

    public function getJwtToken(): ?string
    {
        return $this->jwtToken;
    }

    public function setJwtToken(?string $jwtToken): void
    {
        $this->jwtToken = $jwtToken;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(?string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
