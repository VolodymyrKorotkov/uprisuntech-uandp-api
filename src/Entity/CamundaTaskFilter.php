<?php declare(strict_types=1);

namespace App\Entity;

use App\Repository\CamundaTaskFilterRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: CamundaTaskFilterRepository::class)]
#[ORM\Table(schema: 'application_flow')]
#[ORM\HasLifecycleCallbacks]
class CamundaTaskFilter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[NotBlank]
    private ?string $property;

    #[NotBlank]
    #[ORM\Column]
    private ?string $value;

    #[ORM\ManyToOne]
    private CamundaStrategy $camundaStrategy;

    public function __toString(): string
    {
        return $this->property.': '.$this->value;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getProperty(): ?string
    {
        return $this->property;
    }

    public function setProperty(?string $property): void
    {
        $this->property = $property;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    public function getCamundaStrategy(): ?CamundaStrategy
    {
        return $this->camundaStrategy;
    }

    public function setCamundaStrategy(?CamundaStrategy $camundaStrategy): static
    {
        $this->camundaStrategy = $camundaStrategy;

        return $this;
    }
}
