<?php declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\Controller\ApiPlatform\Dto\FormioTokenDto;
use App\Controller\ApiPlatform\FormIoMetadataProvider;
use App\Controller\ApiPlatform\FormIoTokenApiPlatformProvider;
use App\Enum\AppRoutePrefixEnum;
use App\Enum\UserRoleEnum;
use App\Repository\FormIoRepository;
use App\Serializer\SerializerGroupsEnum;
use App\Service\FormIoClient\Dto\FormMetadata\FormMetadataDto;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Valid;

#[Get(
    uriTemplate: '/form_ios/{id}/metadata',
    routePrefix: AppRoutePrefixEnum::ADMIN->value,
    stateless: false,
    security: "is_granted('".UserRoleEnum::ROLE_FORMIO_VIEW->value."')",
    provider: FormIoMetadataProvider::class
)]
#[Get(
    uriTemplate: '/form_ios/jwt-token',
    routePrefix: AppRoutePrefixEnum::API_ACCOUNT->value,
    output: FormioTokenDto::class,
    provider: FormIoTokenApiPlatformProvider::class
)]

#[UniqueEntity(fields: ['formKey'])]
#[UniqueEntity(fields: ['formId'])]
#[UniqueEntity(fields: ['title'])]

#[ORM\Entity(repositoryClass: FormIoRepository::class)]
class FormIo implements IgnoreCheckEntityCrudPermission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([SerializerGroupsEnum::SAFE_VIEW])]
    private ?int $id = null;
    #[ORM\Column(unique: true)]
    #[Groups([SerializerGroupsEnum::SAFE_VIEW])]
    private string|null $formKey = null;

    #[ORM\Column(unique: true, nullable: true)]
    #[Groups([SerializerGroupsEnum::SAFE_VIEW])]
    private string|null $formId = null;

    #[ORM\Column(unique: true, nullable: true)]
    #[Groups([SerializerGroupsEnum::SAFE_VIEW])]
    private string|null $title = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private bool|null $applicationPublicForm = false;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private bool|null $installerProposalForm = false;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private bool|null $managerProposalForm = false;

    #[ORM\Column(nullable: true)]
    #[Groups([SerializerGroupsEnum::SAFE_VIEW])]
    private string|null $filterByApplicationNumberPath = null;

    #[ORM\Column(nullable: true)]
    #[Groups([SerializerGroupsEnum::SAFE_VIEW])]
    private string|null $filterByStatusPath = null;

    #[ORM\Column(nullable: true)]
    #[Groups([SerializerGroupsEnum::SAFE_VIEW])]
    private string|null $confirmedStatusValue = null;

    #[ORM\Column(nullable: true)]
    #[Groups([SerializerGroupsEnum::SAFE_VIEW])]
    private string|null $draftStatusValue = null;

    #[ORM\Column(nullable: true)]
    #[Groups([SerializerGroupsEnum::SAFE_VIEW])]
    private string|null $emailPropertyPath = null;

    #[ORM\Column(nullable: true)]
    #[Groups([SerializerGroupsEnum::SAFE_VIEW])]
    private string|null $applicationNumberPropertyPath = null;

    #[ORM\Column(nullable: true)]
    #[Groups([SerializerGroupsEnum::SAFE_VIEW])]
    private string|null $firstNamePropertyPath = null;

    #[ORM\Column(nullable: true)]
    #[Groups([SerializerGroupsEnum::SAFE_VIEW])]
    private string|null $lastNamePropertyPath = null;

    #[ORM\Column(nullable: true)]
    #[Groups([SerializerGroupsEnum::SAFE_VIEW])]
    private string|null $applicationResourcePath = null;

    #[ORM\Column(nullable: true)]
    #[Groups([SerializerGroupsEnum::SAFE_VIEW])]
    private string|null $addressPropertyPath = null;

    #[ORM\Column(nullable: true)]
    #[Groups([SerializerGroupsEnum::SAFE_VIEW])]
    private string|null $zipCodePropertyPath = null;

    #[ORM\OneToMany(mappedBy: 'parentForm', targetEntity: FormIoProcessResource::class, cascade: ['all'], orphanRemoval: true)]
    private Collection $processResources;

    #[ORM\OneToMany(mappedBy: 'resource', targetEntity: FormIoProcessResource::class, cascade: ['all'], orphanRemoval: true)]
    private Collection $inProcessResources;

    #[ORM\OneToMany(mappedBy: 'form', targetEntity: FormProcessSubmission::class, cascade: ['all'], orphanRemoval: true)]
    private Collection $processSubmissions;

    #[Valid]
    #[ORM\ManyToOne(targetEntity: ApplicationType::class)]
    private ApplicationType|null $startedProcessType;

    #[ORM\Column(nullable: true)]
    #[Groups([SerializerGroupsEnum::SAFE_VIEW])]
    private string|null $statusPath = null;

    public function __construct()
    {
        $this->processResources = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->title ?? $this->formKey ?? 'Form #'.$this->id;
    }

    public function getFirstNamePropertyPath(): ?string
    {
        return $this->firstNamePropertyPath;
    }

    public function setFirstNamePropertyPath(?string $firstNamePropertyPath): void
    {
        $this->firstNamePropertyPath = $firstNamePropertyPath;
    }

    public function getLastNamePropertyPath(): ?string
    {
        return $this->lastNamePropertyPath;
    }

    public function setLastNamePropertyPath(?string $lastNamePropertyPath): void
    {
        $this->lastNamePropertyPath = $lastNamePropertyPath;
    }

    public function getApplicationResourcePath(): ?string
    {
        return $this->applicationResourcePath;
    }

    public function setApplicationResourcePath(?string $applicationResourcePath): void
    {
        $this->applicationResourcePath = $applicationResourcePath;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getFormKey(): ?string
    {
        return $this->formKey;
    }

    public function setFormKey(?string $formKey): void
    {
        $this->formKey = $formKey;
    }

    public function hasFormKey(): bool
    {
        return null !== $this->formKey;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function fillFromApiData(FormMetadataDto $formApiData): void
    {
        $this->formKey = $formApiData->path;
        $this->title = $formApiData->title;
    }

    public function getFormId(): ?string
    {
        return $this->formId;
    }

    public function setFormId(?string $formId): void
    {
        $this->formId = $formId;
    }

    public function isApplicationPublicForm(): bool
    {
        return $this->applicationPublicForm === true;
    }

    public function setApplicationPublicForm(bool $applicationPublicForm): void
    {
        $this->applicationPublicForm = $applicationPublicForm;
    }

    public function isInstallerProposalForm(): bool
    {
        return $this->installerProposalForm === true;
    }

    public function setInstallerProposalForm(?bool $isManagerProposalUser): void
    {
        $this->installerProposalForm = $isManagerProposalUser;
    }

    public function getFilterByApplicationNumberPath(): ?string
    {
        return $this->filterByApplicationNumberPath;
    }

    public function setFilterByApplicationNumberPath(?string $filterByApplicationNumberPath): void
    {
        $this->filterByApplicationNumberPath = $filterByApplicationNumberPath;
    }

    public function getEmailPropertyPath(): ?string
    {
        return $this->emailPropertyPath;
    }

    public function setEmailPropertyPath(?string $emailPropertyPath): void
    {
        $this->emailPropertyPath = $emailPropertyPath;
    }

    public function getApplicationNumberPropertyPath(): ?string
    {
        return $this->applicationNumberPropertyPath;
    }

    public function setApplicationNumberPropertyPath(?string $applicationNumberPropertyPath): void
    {
        $this->applicationNumberPropertyPath = $applicationNumberPropertyPath;
    }

    public function getAddressPropertyPath(): ?string
    {
        return $this->addressPropertyPath;
    }

    public function setAddressPropertyPath(?string $addressPropertyPath): void
    {
        $this->addressPropertyPath = $addressPropertyPath;
    }

    /**
     * @return Collection<int, FormIoProcessResource>
     */
    public function getProcessResources(): Collection
    {
        return $this->processResources;
    }

    public function addProcessResource(FormIoProcessResource $processResource): static
    {
        if (!$this->processResources->contains($processResource)) {
            $this->processResources->add($processResource);
            $processResource->setParentForm($this);
        }

        return $this;
    }

    public function removeProcessResource(FormIoProcessResource $processResource): static
    {
        $this->processResources->removeElement($processResource);

        return $this;
    }

    public function getFilterByStatusPath(): ?string
    {
        return $this->filterByStatusPath;
    }

    public function setFilterByStatusPath(?string $filterByStatusPath): void
    {
        $this->filterByStatusPath = $filterByStatusPath;
    }

    public function getConfirmedStatusValue(): ?string
    {
        return $this->confirmedStatusValue;
    }

    public function setConfirmedStatusValue(?string $confirmedStatusValue): void
    {
        $this->confirmedStatusValue = $confirmedStatusValue;
    }

    public function isManagerProposalForm(): bool
    {
        return $this->managerProposalForm === true;
    }

    public function setManagerProposalForm(?bool $managerProposalForm): void
    {
        $this->managerProposalForm = $managerProposalForm;
    }

    public function getStartedProcessType(): ?ApplicationType
    {
        return $this->startedProcessType;
    }

    public function setStartedProcessType(?ApplicationType $startedProcessType): void
    {
        $this->startedProcessType = $startedProcessType;
    }

    public function getStatusPath(): ?string
    {
        return $this->statusPath;
    }

    public function setStatusPath(?string $statusPath): void
    {
        $this->statusPath = $statusPath;
    }

    public function getDraftStatusValue(): ?string
    {
        return $this->draftStatusValue;
    }

    public function setDraftStatusValue(?string $draftStatusValue): void
    {
        $this->draftStatusValue = $draftStatusValue;
    }

    public function getZipCodePropertyPath(): ?string
    {
        return $this->zipCodePropertyPath;
    }

    public function setZipCodePropertyPath(?string $zipCodePropertyPath): void
    {
        $this->zipCodePropertyPath = $zipCodePropertyPath;
    }
}
