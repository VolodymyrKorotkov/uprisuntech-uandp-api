<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SiteRedirectRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: SiteRedirectRepository::class)]
class SiteRedirect
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[NotBlank]
    #[ORM\Column(type: "string", length: 255)]
    private ?string $redirectUrl;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $authSuccessRedirectUrl;

    #[NotBlank]
    #[ORM\Column(type: "string", length: 255, unique: true)]
    private ?string $alias;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }

    public function setRedirectUrl(?string $redirectUrl): static
    {
        $this->redirectUrl = $redirectUrl;

        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(?string $alias): void
    {
        $this->alias = $alias;
    }

    public function getAuthSuccessRedirectUrl(): ?string
    {
        return $this->authSuccessRedirectUrl;
    }

    public function hasAuthSuccessRedirectUrl(): bool
    {
        return null !== $this->authSuccessRedirectUrl;
    }

    public function setAuthSuccessRedirectUrl(?string $authSuccessRedirectUrl): void
    {
        $this->authSuccessRedirectUrl = $authSuccessRedirectUrl;
    }
}
