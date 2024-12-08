<?php

namespace App\Entity;

use App\Repository\FormIoProcessResourceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: FormIoProcessResourceRepository::class)]
class FormIoProcessResource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[NotBlank]
    #[ORM\ManyToOne]
    private FormIo $parentForm;

    #[ORM\ManyToOne]
    private FormIo $resource;

    #[ORM\Column()]
    private string $path;

    public function __toString(): string
    {
        return (string) $this->resource;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParentForm(): FormIo
    {
        return $this->parentForm;
    }

    public function setParentForm(FormIo $parentForm): void
    {
        $this->parentForm = $parentForm;
    }

    public function getResource(): FormIo
    {
        return $this->resource;
    }

    public function setResource(FormIo $resource): void
    {
        $this->resource = $resource;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }
}
