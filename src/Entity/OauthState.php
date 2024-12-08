<?php

namespace App\Entity;

use App\Repository\OauthStateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OauthStateRepository::class)]
class OauthState
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private string $state;

    #[ORM\Column(nullable: true)]
    private ?string $userState;

    #[ORM\ManyToOne]
    private ?SiteRedirect $siteRedirect = null;

    public function __construct()
    {
        $this->state = uniqid();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getUserState(): ?string
    {
        return $this->userState;
    }

    public function setUserState(?string $userState): static
    {
        $this->userState = $userState;

        return $this;
    }

    public function getSiteRedirect(): ?SiteRedirect
    {
        return $this->siteRedirect;
    }

    public function setSiteRedirect(?SiteRedirect $siteRedirect): static
    {
        $this->siteRedirect = $siteRedirect;

        return $this;
    }
}
