<?php declare(strict_types=1);

namespace App\Entity;

use App\Enum\ApplicationStrategyEnum;
use App\Repository\NativeStrategyRepository;
use App\Serializer\SerializerGroupsEnum;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

#[ORM\Entity(repositoryClass: NativeStrategyRepository::class)]
class NativeStrategy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[Groups([SerializerGroupsEnum::SAFE_VIEW])]
    private FormIo|null $form;

    #[Valid]
    #[ORM\OneToOne(inversedBy: 'nativeStrategy', cascade: ['all'])]
    private ApplicationType $type;

    #[Valid]
    #[ORM\ManyToOne(cascade: ['all'])]
    private ApplicationType $nextType;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private bool|null $assignForProcessStarter;

    #[ORM\ManyToOne]
    private FormIo|null $tableForm;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private bool|null $neverComplete = false;

    public function __construct()
    {
        $this->type = new ApplicationType();
        $this->type->setStrategyType(ApplicationStrategyEnum::NATIVE);
    }

    public function __toString(): string
    {
        return $this->title ?? '#'.$this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getForm(): ?FormIo
    {
        return $this->form;
    }

    public function setForm(?FormIo $form): void
    {
        $this->form = $form;
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

    #[NotBlank]
    public function getAlias(): ?string
    {
        return $this->type->getAlias();
    }

    public function setAlias(?string $alias): void
    {
        $this->type->setAlias($alias);
    }

    public function setType(ApplicationType $type): void
    {
        $this->type = $type;
    }

    public function isAllowStartProcess(): bool
    {
        return $this->type->isAllowStartProcess();
    }

    public function setAllowStartProcess(?bool $allow): void
    {
        $this->type->setAllowStartProcess($allow);
    }

    public function nextIsEmpty(): bool
    {
        return empty($this->nextType);
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

    public function getNextType(): ApplicationType
    {
        return $this->nextType;
    }

    public function setNextType(ApplicationType $nextType): void
    {
        $this->nextType = $nextType;
    }

    public function isEnabled(): bool
    {
        return $this->type->isEnabled();
    }

    public function setEnabled(?bool $default): void
    {
        $this->type->setEnabled($default === true);
    }

    public function isAssignForProcessStarter(): bool
    {
        return $this->assignForProcessStarter === true;
    }

    public function setAssignForProcessStarter(?bool $assignForProcessStarter): void
    {
        $this->assignForProcessStarter = $assignForProcessStarter;
    }

    public function getTableForm(): ?FormIo
    {
        return $this->tableForm;
    }

    public function setTableForm(?FormIo $tableForm): void
    {
        $this->tableForm = $tableForm;
    }

    public function getNeverComplete(): bool
    {
        return $this->neverComplete === true;
    }

    public function setNeverComplete(?bool $neverComplete): void
    {
        $this->neverComplete = $neverComplete;
    }

    public function isNeverComplete(): ?bool
    {
        return $this->neverComplete;
    }
}
