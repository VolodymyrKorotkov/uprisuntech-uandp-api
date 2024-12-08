<?php declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Enum\ApplicationStrategyEnum;
use App\Enum\AppRoutePrefixEnum;
use App\Repository\ApplicationTypeRepository;
use App\Serializer\SerializerGroupsEnum;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints\NotBlank;

#[GetCollection(
    routePrefix: AppRoutePrefixEnum::API_ACCOUNT->value,
    paginationEnabled: true,
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
    normalizationContext: ['groups' => [SerializerGroupsEnum::SAFE_VIEW]],
)]
#[Get(
    routePrefix: AppRoutePrefixEnum::API_PUBLIC->value,
    normalizationContext: ['groups' => [SerializerGroupsEnum::SAFE_VIEW]],
)]

#[ApiFilter(SearchFilter::class, properties: ['alias' => 'exact'])]

#[UniqueEntity(fields: ['title'])]
#[UniqueEntity(fields: ['alias'])]

#[ORM\Entity(repositoryClass: ApplicationTypeRepository::class)]
class ApplicationType implements IgnoreEntityOwnerViewPermission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([SerializerGroupsEnum::SAFE_VIEW, ApplicationTask::CREATE_GROUP])]
    private ?int $id = null;

    #[NotBlank]
    #[ORM\Column(unique: true)]
    #[Groups(SerializerGroupsEnum::SAFE_VIEW)]
    private string|null $alias = null;

    #[NotBlank]
    #[ORM\Column(unique: true)]
    #[Groups(SerializerGroupsEnum::SAFE_VIEW)]
    private string|null $title = null;

    #[ORM\Column]
    #[NotBlank]
    #[Groups(SerializerGroupsEnum::SAFE_VIEW)]
    private string|null $role = null;

    #[Groups(SerializerGroupsEnum::SAFE_VIEW)]
    #[ORM\Column]
    private ApplicationStrategyEnum $strategyType = ApplicationStrategyEnum::NATIVE;

    #[ORM\OneToOne(mappedBy: 'type', cascade: ['all'], orphanRemoval: true)]
    private ?CamundaStrategy $camundaStrategy = null;

    #[ORM\OneToOne(mappedBy: 'type', cascade: ['all'], orphanRemoval: true)]
    private ?NativeStrategy $nativeStrategy = null;

    #[ORM\Column(type: 'boolean')]
    private bool $allowStartProcess = true;

    #[ORM\Column(type: 'boolean')]
    private bool $enabled = false;

    public function getCamundaStrategy(): ?CamundaStrategy
    {
        return $this->camundaStrategy;
    }

    public function setCamundaStrategy(?CamundaStrategy $camundaStrategy): void
    {
        $this->camundaStrategy = $camundaStrategy;
        $camundaStrategy?->setType($this);
    }

    public function getNativeStrategy(): ?NativeStrategy
    {
        return $this->nativeStrategy;
    }

    public function setNativeStrategy(?NativeStrategy $nativeStrategy): void
    {
        $this->nativeStrategy = $nativeStrategy;
        $nativeStrategy?->setType($this);
    }

    public function __toString(): string
    {
        return $this->title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function hasCamundaStrategy(): bool
    {
        return null !== $this->camundaStrategy;
    }

    public function hasNativeStrategy(): bool
    {
        return null !== $this->nativeStrategy;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        if (!$this->alias){
            $this->alias = (new AsciiSlugger())->slug($this->title)->toString();
        }

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

    public function getStrategyType(): ApplicationStrategyEnum
    {
        return $this->strategyType;
    }

    public function setStrategyType(ApplicationStrategyEnum $strategyType): void
    {
        $this->strategyType = $strategyType;
    }

    #[Groups(SerializerGroupsEnum::SAFE_VIEW)]
    public function getDefaultForm(): FormIo
    {
        return $this->strategyType->getDefaultForm($this);
    }

    public function getTableForm(): ?FormIo
    {
        return $this->strategyType->getTableForm($this);
    }

    public function isAllowStartProcess(): bool
    {
        return $this->allowStartProcess;
    }

    public function setAllowStartProcess(bool $allowStartProcess): void
    {
        $this->allowStartProcess = $allowStartProcess;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): void
    {
        $this->role = $role;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }
}
