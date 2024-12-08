<?php declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\ApiPlatform\QuotaProposalsSubmissionsProvider;
use App\Controller\ApiPlatform\QuotaSubmissionsProvider;
use App\Enum\AppRoutePrefixEnum;
use App\Repository\ApplicationProcessRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[GetCollection(
    routePrefix: AppRoutePrefixEnum::API_ACCOUNT->value,
    paginationEnabled: true,
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
    order: ['id' => 'DESC']
)]

#[GetCollection(
    uriTemplate: '/application_processes/quota',
    routePrefix: AppRoutePrefixEnum::API_ACCOUNT->value,
    paginationEnabled: true,
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
    provider: QuotaSubmissionsProvider::class
)]

#[GetCollection(
    uriTemplate: '/application_processes/quota_proposals',
    routePrefix: AppRoutePrefixEnum::API_ACCOUNT->value,
    paginationEnabled: true,
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
    provider: QuotaProposalsSubmissionsProvider::class
)]

#[ORM\Entity(repositoryClass: ApplicationProcessRepository::class)]
#[UniqueEntity(fields: ['processInstanceId'])]
class ApplicationProcess implements IgnoreCheckEntityCrudPermission
{
    #[ApiProperty(identifier: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private User|null $user = null;

    #[ApiProperty(identifier: true)]
    #[ORM\Column]
    private string $processInstanceId;

    #[ORM\Column(nullable: true)]
    private string|null $title;

    #[ORM\ManyToOne()]
    private ApplicationType $type;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private \DateTimeInterface $processCreatedAt;

    public function __construct()
    {
        $this->processCreatedAt = new \DateTime();
    }

    public function __toString(): string
    {
        return $this->getTitle();
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getProcessInstanceId(): string
    {
        return $this->processInstanceId;
    }

    public function setProcessInstanceId(string $processInstanceId): void
    {
        $this->processInstanceId = $processInstanceId;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ApplicationType
    {
        return $this->type;
    }

    public function setType(ApplicationType $type): void
    {
        $this->type = $type;
    }

    public function getProcessCreatedAt(): \DateTimeInterface
    {
        return $this->processCreatedAt;
    }

    public function setProcessCreatedAt(\DateTimeInterface $processCreatedAt): void
    {
        $this->processCreatedAt = $processCreatedAt;
    }

    public function getTitle(): string
    {
        return $this->title ?? ($this->type->getTitle() . ' (' . $this->user . ') #' . $this->processInstanceId);
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
}
