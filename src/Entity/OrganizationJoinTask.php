<?php declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\Enum\AppRoutePrefixEnum;
use App\Enum\OrganizationJoinStatusEnum;
use App\Enum\UserRoleEnum;
use App\Repository\OrganizationJoinTaskRepository;
use App\Security\EntityHasOwnerInterface;
use App\Serializer\SerializerGroupsEnum;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

#[Patch(
    routePrefix: AppRoutePrefixEnum::API_ACCOUNT->value,
    normalizationContext: self::NORMALIZATION_CONTEXT,
    denormalizationContext: self::DENORMALIZATION_CONTEXT,
)]
#[GetCollection(
    routePrefix: AppRoutePrefixEnum::API_ACCOUNT->value,
    normalizationContext: self::NORMALIZATION_CONTEXT,
    paginationEnabled: true,
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
)]
#[Get(
    routePrefix: AppRoutePrefixEnum::API_ACCOUNT->value,
    normalizationContext: self::NORMALIZATION_CONTEXT,
)]

#[ApiFilter(SearchFilter::class, properties: ['status' => 'exact'])]

#[ORM\Entity(repositoryClass: OrganizationJoinTaskRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class OrganizationJoinTask implements EntityHasOwnerInterface
{
    private const NORMALIZATION_CONTEXT = [
        'groups' => [
            OrganizationJoinFlow::GROUP_VIEW,
            OrganizationJoinTask::GROUP_VIEW,
            SerializerGroupsEnum::SAFE_VIEW
        ]
    ];

    private const DENORMALIZATION_CONTEXT = [
        'groups' => [
            OrganizationJoinFlow::GROUP_SAVE,
            OrganizationJoinTask::GROUP_SAVE,
        ]
    ];

    public const GROUP_SAVE = 'OrganizationJoinTask.Save';
    public const GROUP_VIEW = 'OrganizationJoinTask.Save';

    #[Groups([OrganizationJoinFlow::GROUP_VIEW])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups([OrganizationJoinFlow::GROUP_SAVE, OrganizationJoinFlow::GROUP_VIEW])]
    #[ORM\Column(enumType: OrganizationJoinStatusEnum::class)]
    private OrganizationJoinStatusEnum $status = OrganizationJoinStatusEnum::IN_PROGRESS;

    #[Groups([OrganizationJoinFlow::GROUP_VIEW])]
    #[ORM\ManyToOne(targetEntity: OrganizationJoinFlow::class)]
    private OrganizationJoinFlow|null $flow = null;

    #[NotBlank]
    #[Valid]
    #[Groups([OrganizationJoinFlow::GROUP_SAVE, OrganizationJoinFlow::GROUP_VIEW])]
    #[ORM\ManyToOne(targetEntity: OrganizationJoinFlowData::class, cascade: ['All'])]
    private OrganizationJoinFlowData|null $data;

    #[ORM\ManyToOne(targetEntity:  User::class)]
    private ?User $user = null;

    #[Groups([OrganizationJoinFlow::GROUP_VIEW])]
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface|null $updatedAt = null;

    #[Groups([OrganizationJoinFlow::GROUP_VIEW])]
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface|null $createdAt = null;

    public function __construct()
    {
        $this->data = new OrganizationJoinFlowData();
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

    public function getStatus(): OrganizationJoinStatusEnum
    {
        return $this->status;
    }

    public function setStatus(OrganizationJoinStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getFlow(): ?OrganizationJoinFlow
    {
        return $this->flow;
    }

    public function setFlow(?OrganizationJoinFlow $flow): static
    {
        $this->flow = $flow;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getData(): ?OrganizationJoinFlowData
    {
        return $this->data;
    }

    public function setData(?OrganizationJoinFlowData $data): void
    {
        $this->data = $data;
    }

    public function getUserIdentifier(): string
    {
        return $this->user->getUserIdentifier();
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getEdrpou(): ?string
    {
        return $this->data->getEdrpou();
    }

    public function setEdrpou(?string $edrpou): void
    {
        $this->data->setEdrpou($edrpou);
    }

    #[NotBlank]
    public function getTitle(): ?string
    {
        return $this->data->getTitle();
    }

    public function setTitle(?string $title): void
    {
        $this->data->setTitle($title);
    }

    public function getRole(): ?UserRoleEnum
    {
        return $this->data->getRole();
    }

    public function setRole(?UserRoleEnum $role): void
    {
        $this->data->setRole($role);
    }
}
