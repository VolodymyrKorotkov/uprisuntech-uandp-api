<?php

namespace App\Entity;

use App\Enum\ApplicationStrategyEnum;
use App\Repository\CamundaStrategyRepository;
use App\Serializer\SerializerGroupsEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

#[ORM\Entity(repositoryClass: CamundaStrategyRepository::class)]
class CamundaStrategy implements IgnoreCheckEntityCrudPermission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[NotBlank]
    #[ORM\Column(nullable: true)]
    private ?string $camundaAlias;

    #[NotBlank]
    #[ORM\Column(nullable: true)]
    private ?string $tenantId;

    #[ORM\Column(type: 'boolean')]
    private bool $allTasksOneUser = true;

    #[ORM\ManyToOne(targetEntity: FormIo::class)]
    #[NotBlank]
    #[Groups([SerializerGroupsEnum::SAFE_VIEW])]
    private FormIo|null $defaultForm;

    #[Valid]
    #[ORM\OneToOne(inversedBy: 'camundaStrategy', targetEntity: ApplicationType::class, cascade: ['all'], orphanRemoval: true)]
    private ApplicationType $type;

    #[ORM\OneToMany(mappedBy: 'camundaStrategy', targetEntity: CamundaTaskFilter::class, cascade: ['all'], orphanRemoval: true)]
    private Collection $filters;

    #[NotBlank]
    #[ORM\ManyToOne]
    private FormIo|null $tableForm;

    public function __construct()
    {
        $this->type = new ApplicationType();
        $this->type->setStrategyType(ApplicationStrategyEnum::CAMUNDA);
        $this->filters = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->title ?? $this->camundaAlias . '(' . $this->tenantId . ')' ?? '#'.$this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCamundaAlias(): ?string
    {
        return $this->camundaAlias;
    }

    public function setCamundaAlias(string $camundaAlias): static
    {
        $this->camundaAlias = $camundaAlias;

        return $this;
    }

    public function getTenantId(): ?string
    {
        return $this->tenantId;
    }

    public function setTenantId(?string $tenantId): void
    {
        $this->tenantId = $tenantId;
    }

    public function getDefaultForm(): ?FormIo
    {
        return $this->defaultForm;
    }

    public function setDefaultForm(?FormIo $defaultForm): void
    {
        $this->defaultForm = $defaultForm;
    }

    #[NotBlank]
    public function getTitle(): ?string
    {
        return $this->type->getTitle();
    }

    public function setTitle(?string $title): void
    {
        $this->type->setTitle($title);
    }

    public function getType(): ApplicationType
    {
        return $this->type;
    }

    public function getAlias(): ?string
    {
        return $this->type->getAlias();
    }

    public function setAlias(?string $alias): void
    {
        $this->type->setAlias($alias);
    }

    public function isAllowStartProcess(): bool
    {
        return $this->type->isAllowStartProcess();
    }

    public function setAllowStartProcess(?bool $allow): void
    {
        $this->type->setAllowStartProcess($allow);
    }

    public function setType(ApplicationType $type): void
    {
        $this->type = $type;
    }

    /**
     * @return Collection<int, CamundaTaskFilter>
     */
    public function getFilters(): Collection
    {
        return $this->filters;
    }

    public function addFilter(CamundaTaskFilter $filter): static
    {
        if (!$this->filters->contains($filter)) {
            $this->filters->add($filter);
            $filter->setCamundaStrategy($this);
        }

        return $this;
    }

    public function removeFilter(CamundaTaskFilter $filter): static
    {
        $this->filters->removeElement($filter);

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->type->isEnabled();
    }

    public function setEnabled(?bool $default): void
    {
        $this->type->setEnabled($default === true);
    }

    #[NotBlank]
    public function getRole(): ?string
    {
        return $this->type->getRole();
    }

    public function setRole(?string $role): void
    {
        $this->type->setRole($role);
    }

    public function getTableForm(): ?FormIo
    {
        return $this->tableForm;
    }

    public function setTableForm(?FormIo $tableForm): void
    {
        $this->tableForm = $tableForm;
    }

    public function isAllTasksOneUser(): ?bool
    {
        return $this->allTasksOneUser;
    }

    public function setAllTasksOneUser(bool $allTasksOneUser): static
    {
        $this->allTasksOneUser = $allTasksOneUser;

        return $this;
    }
}
