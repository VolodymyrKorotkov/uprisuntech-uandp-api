<?php declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use App\Controller\ApiPlatform\AggregateTaskAssignerProcessor;
use App\Controller\ApiPlatform\AggregateTaskCollectionProvider;
use App\Controller\ApiPlatform\AggregateTaskCompleteProcessor;
use App\Controller\ApiPlatform\AggregateTaskItemProvider;
use App\Entity\Sub\DataTimesFieldsTrait;
use App\Enum\AppRoutePrefixEnum;
use App\Repository\ApplicationTask\NativeTaskRepository;
use App\Serializer\SerializerGroupsEnum;
use App\Service\AggregateTaskAssigner\AssignTaskDto;
use App\Service\AggregateTaskCompleter\CompleteTaskDto;
use App\Service\AggregateTaskProvider\Dto\AggregateTaskDto;
use App\Service\AggregateTaskProvider\Dto\ProcessTasksCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Get(
    uriTemplate: '/application_tasks',
    routePrefix: AppRoutePrefixEnum::API_ACCOUNT->value,
    paginationEnabled: true,
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
    order: ['updatedAt' => 'DESC', 'completed' => 'DESC'],
    normalizationContext: self::NORMALIZATION_CONTEXT,
    output: ProcessTasksCollection::class,
    provider: AggregateTaskCollectionProvider::class
)]

#[Put(
    uriTemplate: '/application_tasks/complete',
    routePrefix: AppRoutePrefixEnum::API_ACCOUNT->value,
    normalizationContext: self::NORMALIZATION_CONTEXT,
    input: CompleteTaskDto::class,
    output: AggregateTaskDto::class,
    processor: AggregateTaskCompleteProcessor::class
)]

#[Put(
    uriTemplate: '/application_tasks/assign',
    routePrefix: AppRoutePrefixEnum::API_ACCOUNT->value,
    normalizationContext: self::NORMALIZATION_CONTEXT,
    input: AssignTaskDto::class,
    output: AggregateTaskDto::class,
    processor: AggregateTaskAssignerProcessor::class
)]

#[Get(
    uriTemplate: '/application_tasks/{taskId}',
    routePrefix: AppRoutePrefixEnum::API_ACCOUNT->value,
    normalizationContext: self::NORMALIZATION_CONTEXT + [ AbstractNormalizer::OBJECT_TO_POPULATE => new AggregateTaskDto()],
    output: AggregateTaskDto::class,
    provider: AggregateTaskItemProvider::class
)]

#[ORM\Entity(repositoryClass: NativeTaskRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class ApplicationTask implements IgnoreCheckEntityCrudPermission
{
    use DataTimesFieldsTrait;

    public const SAFE_VIEW_GROUP = SerializerGroupsEnum::SAFE_VIEW;
    public const VIEW_GROUP = 'ApplicationTask.assoc';
    public const UPDATE_GROUP = 'ApplicationTask.update';
    public const CREATE_GROUP = 'ApplicationTask.create';

    const NORMALIZATION_CONTEXT = [
        'groups' => [
            self::SAFE_VIEW_GROUP,
            self::VIEW_GROUP
        ],
    ];

    #[ApiProperty(identifier: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([self::VIEW_GROUP, self::UPDATE_GROUP])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups([SerializerGroupsEnum::SAFE_VIEW])]
    private string|null $processInstanceId = null;

    #[ApiProperty(identifier: true)]
    #[ORM\Column]
    #[Groups([SerializerGroupsEnum::SAFE_VIEW])]
    private string $taskId;

    #[ORM\Column(type: 'boolean')]
    private bool $completed = false;

    #[ORM\ManyToOne]
    private FormIo|null $form = null;

    #[ORM\Column(nullable: true)]
    #[Groups([self::VIEW_GROUP])]
    private string|null $userIdentifier = null;

    #[ORM\Column(nullable: true)]
    #[Groups([self::VIEW_GROUP])]
    private string|null $role = null;

    #[NotBlank]
    #[Groups([self::VIEW_GROUP, self::CREATE_GROUP])]
    #[ORM\ManyToOne()]
    private ?ApplicationType $type = null;

    #[ManyToOne(targetEntity: ApplicationTask::class)]
    private ApplicationTask|null $parentTask = null;

    #[ORM\Column(nullable: true)]
    private string|null $title;

    public function __construct()
    {
        $this->updatedAt = new \DateTime();
        $this->createdAt = new \DateTime();
    }

    public function __toString(): string
    {
        return $this->getTitle();
    }

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function hasUser(): bool
    {
        return null !== $this->userIdentifier;
    }

    public function getUserIdentifier(): ?string
    {
        return $this->userIdentifier;
    }

    public function setUserIdentifier(?string $userIdentity): static
    {
        $this->userIdentifier = $userIdentity;

        return $this;
    }

    public function getType(): ?ApplicationType
    {
        return $this->type;
    }

    public function setType(?ApplicationType $type): void
    {
        $this->type = $type;
    }

    public function getProcessInstanceId(): string
    {
        return $this->processInstanceId;
    }

    public function hasProcessInstanceId(): bool
    {
        return null !== $this->processInstanceId;
    }

    public function setProcessInstanceId(string $processInstanceId): void
    {
        $this->processInstanceId = $processInstanceId;
    }

    public function getTaskId(): string
    {
        return $this->taskId;
    }

    public function setTaskId(string $taskId): self
    {
        $this->taskId = $taskId;
        return $this;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): void
    {
        $this->completed = $completed;
    }

    public function getForm(): ?FormIo
    {
        return $this->form;
    }

    public function setForm(?FormIo $form): void
    {
        $this->form = $form;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): void
    {
        $this->role = $role;
    }

    #[Groups(self::VIEW_GROUP)]
    public function getLockForUpdate(): bool
    {
        return $this->completed;
    }

    #[Groups(self::VIEW_GROUP)]
    public function getBelongsOrganization(): bool
    {
        return false;
    }

    #[Groups(self::VIEW_GROUP)]
    public function getProcessed(): bool
    {
        return $this->completed;
    }

    #[Groups(self::VIEW_GROUP)]
    public function isDraft(): bool
    {
        return false;
    }

    public function getParentTask(): ?ApplicationTask
    {
        return $this->parentTask;
    }

    public function setParentTask(?ApplicationTask $parentTask): void
    {
        $this->parentTask = $parentTask;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title ?? $this->type->getTitle() . ' "' . $this->getForm()->getTitle() . '" #' . $this->processInstanceId;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function setTitleFromProcessInstance(ApplicationProcess $process): void
    {
        $this->title = $process->getTitle().' '.$this->form->getTitle();
    }
}
