<?php declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Entity\Sub\EntityHasApplicationUserInterface;
use App\Enum\AppRoutePrefixEnum;
use App\Enum\OrganizationJoinInviteStatusEnum;
use App\Repository\OrganizationJoinInviteRepository;
use App\Security\EntityHasOwnerInterface;
use App\Service\OrganizationJoinInviteProcessor\InviteUserStateProcessor;
use App\Service\OrganizationJoinInviteProcessor\ProcessInviteUserStateDto;
use App\Validator\UserHasDefaaultOrganization\UserHasDefaultOrganization;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Post(
    routePrefix: AppRoutePrefixEnum::API_ACCOUNT->value,
    denormalizationContext: ['groups' => [self::GROUP_SAVE]]
)]
#[Delete(
    routePrefix: AppRoutePrefixEnum::API_ACCOUNT->value,
)]

#[Post(
    uriTemplate: '/organization_join_invites/process',
    routePrefix: AppRoutePrefixEnum::API_ACCOUNT->value,
    input: ProcessInviteUserStateDto::class,
    output: OrganizationUserRole::class,
    processor: InviteUserStateProcessor::class
)]

#[GetCollection(
    routePrefix: AppRoutePrefixEnum::API_ACCOUNT->value,
    paginationEnabled: true,
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
)]

#[ApiFilter(SearchFilter::class, properties: ['status' => 'exact'])]

#[UniqueEntity('oauthUserState')]
#[ORM\Entity(repositoryClass: OrganizationJoinInviteRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class OrganizationJoinInvite implements EntityHasOwnerInterface, EntityHasApplicationUserInterface
{
    private const GROUP_SAVE = 'OrganizationJoinInvite.Save';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface|null $updatedAt = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface|null $createdAt = null;

    #[ORM\Column()]
    private string|null $oauthUserState;

    #[Groups([self::GROUP_SAVE])]
    #[NotBlank]
    #[UserHasDefaultOrganization]
    #[ORM\Column()]
    private string|null $drfoCode = null;

    #[ORM\Column(nullable: true)]
    private string|null $inviteUrl = null;

    #[Groups([self::GROUP_SAVE])]
    #[NotBlank]
    #[Email]
    #[ORM\Column()]
    private string|null $email = null;

    #[ORM\ManyToOne()]
    private ?Organization $organization = null;

    #[ORM\Column(enumType: OrganizationJoinInviteStatusEnum::class)]
    private OrganizationJoinInviteStatusEnum $status = OrganizationJoinInviteStatusEnum::INVITED;

    #[ORM\Column(type: 'text', nullable: true)]
    private string|null $comment = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([self::GROUP_SAVE])]
    private ?string $jobTitle = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([self::GROUP_SAVE])]
    private ?string $fullName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([self::GROUP_SAVE])]
    private ?string $phone = null;

    public function __construct()
    {
        $this->oauthUserState = uniqid();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function hasUser(): bool
    {
        return null !== $this->user;
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

    public function getOauthUserState(): ?string
    {
        return $this->oauthUserState;
    }

    public function setOauthUserState(?string $oauthUserState): void
    {
        $this->oauthUserState = $oauthUserState;
    }

    public function getDrfoCode(): ?string
    {
        return $this->drfoCode;
    }

    public function setDrfoCode(?string $drfoCode): void
    {
        $this->drfoCode = $drfoCode;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function setOrganization(?Organization $organization): OrganizationJoinInvite
    {
        $this->organization = $organization;
        return $this;
    }

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function hasOrganization(): bool
    {
        return null !== $this->organization;
    }

    public function getUserIdentifier(): string
    {
        return $this->user->getUserIdentifier();
    }

    public function getStatus(): OrganizationJoinInviteStatusEnum
    {
        return $this->status;
    }

    public function setStatus(OrganizationJoinInviteStatusEnum $status): void
    {
        $this->status = $status;
    }

    public function getInviteUrl(): ?string
    {
        return $this->inviteUrl;
    }

    public function setInviteUrl(?string $inviteUrl): void
    {
        $this->inviteUrl = $inviteUrl;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    public function setJobTitle(?string $jobTitle): static
    {
        $this->jobTitle = $jobTitle;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): static
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }
}
