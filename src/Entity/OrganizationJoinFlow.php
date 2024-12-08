<?php declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Enum\AppRoutePrefixEnum;
use App\Enum\OrganizationJoinStatusEnum;
use App\Repository\OrganizationJoinFlowRepository;
use App\Security\EntityHasOwnerInterface;
use App\Validator\EdrpouValidator\Edrpou;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;


#[Post(
    routePrefix: AppRoutePrefixEnum::API_ACCOUNT->value,
    normalizationContext: ['groups' => [self::GROUP_VIEW]],
    denormalizationContext: ['groups' => [self::GROUP_SAVE]]
)]
#[Patch(
    routePrefix: AppRoutePrefixEnum::API_ACCOUNT->value,
    normalizationContext: ['groups' => [self::GROUP_VIEW]],
    denormalizationContext: ['groups' => [self::GROUP_SAVE]]
)]
#[GetCollection(
    routePrefix: AppRoutePrefixEnum::API_ACCOUNT->value,
    paginationEnabled: true,
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
    normalizationContext: ['groups' => [self::GROUP_VIEW]],
)]

#[ApiFilter(SearchFilter::class, properties: ['status' => 'exact'])]

#[ORM\Entity(repositoryClass: OrganizationJoinFlowRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class OrganizationJoinFlow implements EntityHasOwnerInterface
{
    public const GROUP_SAVE = 'OrganizationJoinFlow.Create';
    public const GROUP_VIEW = 'OrganizationJoinFlow.View';

    #[Groups([self::GROUP_VIEW])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups([OrganizationJoinFlow::GROUP_VIEW])]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user = null;

    #[Groups([self::GROUP_VIEW])]
    #[ORM\Column(enumType: OrganizationJoinStatusEnum::class)]
    private OrganizationJoinStatusEnum $status = OrganizationJoinStatusEnum::IN_PROGRESS;

    #[Edrpou]
    #[NotBlank]
    #[Valid]
    #[Groups([self::GROUP_SAVE, self::GROUP_VIEW])]
    #[ORM\ManyToOne(targetEntity: OrganizationJoinFlowData::class, cascade: ['All'])]
    private OrganizationJoinFlowData|null $data;

    #[Groups([self::GROUP_VIEW])]
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface|null $updatedAt;

    #[Groups([self::GROUP_VIEW])]
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface|null $createdAt;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserIdentifier(): string
    {
        return $this->user->getUserIdentifier();
    }

    public function getData(): ?OrganizationJoinFlowData
    {
        return $this->data;
    }

    public function setData(?OrganizationJoinFlowData $data): static
    {
        $this->data = $data;
        return $this;
    }

    public function getEdrpou(): ?string
    {
        return $this->data->getEdrpou();
    }

    public function setEdrpou(?string $edrpou): void
    {
        $this->data->setEdrpou($edrpou);
    }

    public function getTitle(): ?string
    {
        return $this->data->getTitle();
    }

    public function setTitle(?string $title): void
    {
        $this->data->setTitle($title);
    }
}
