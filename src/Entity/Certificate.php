<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\CertificateUploadController;
use App\Enum\CertificateSource;
use App\Enum\CertificateStatus;
use App\Service\CertificateUploader\Dto\CertificateDto;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(
            uriTemplate: '/certificates/{id}',
            deserialize: false,
            controller: CertificateUploadController::class
        ),
        new Post(
            deserialize: false,
            controller: CertificateUploadController::class,
        ),
        new Delete(),
    ],
    routePrefix: "account",
    normalizationContext: ['groups' => self::GROUP_READ],
    denormalizationContext: ['groups' => self::GROUP_WRITE],
    paginationEnabled: true,
    paginationClientItemsPerPage: true,
    paginationClientEnabled: true
)]

#[Vich\Uploadable]
#[ORM\Entity]
class Certificate implements IgnoreCheckEntityCrudPermission
{
    public const GROUP_WRITE = 'write';
    public const GROUP_READ = 'read';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups([self::GROUP_READ])]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $name;

    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $organization;

    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    #[ORM\Column(type: 'date', nullable: true)]
    private $issueDate;

    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    #[ORM\Column(type: 'date', nullable: true)]
    private $expiryDate;

    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    #[ORM\Column(type: 'boolean')]
    private ?bool $isIndefinite = false;

    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $courseUrl = null;

    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $courseName = null;

    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $courseAuthor = null;

    #[Groups([self::GROUP_READ])]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $certificateFile = null;

    #[Groups([self::GROUP_WRITE])]
    #[Vich\UploadableField(mapping: 'certificate_upload', fileNameProperty: 'certificateFile')]
    public ?File $file = null;

    #[ORM\Column(nullable: true)]
    public ?string $filePath = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private $createdAt;

    #[ORM\Column(type: 'date', nullable: true)]
    private $updatedAt;

    #[Groups([self::GROUP_READ])]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $status = null;

    #[Groups([self::GROUP_READ])]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $courseSource = null;

    #[Groups([self::GROUP_WRITE])]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $userName;


    public static function createFromDto(CertificateDto $dto): self
    {
        $certificate = new self();
        $certificate->setName($dto->name);
        $certificate->setOrganization($dto->organization);

        if($dto->issueDate){
            $certificate->setIssueDate(new \DateTime($dto->issueDate));
        }
        if($dto->expiryDate){
            $certificate->setExpiryDate(new \DateTime($dto->expiryDate));
        }

        $certificate->setIsIndefinite($dto->isIndefinite);
        $certificate->setCourseUrl($dto->courseUrl);
        $certificate->setCourseName($dto->courseName);
        $certificate->setCourseAuthor($dto->courseAuthor);

        $certificate->setStatus(CertificateStatus::STATUS_REVIEW->value);
        $certificate->setCourseSource(CertificateSource::SOURCE_USER_UPLOADED->value);

        $certificate->setCreatedAt(new \DateTimeImmutable());
        $certificate->setUpdatedAt(new \DateTimeImmutable());

        return $certificate;
    }

    public function updateFromDto(CertificateDto $dto): self
    {
        $this->setName($dto->name);
        $this->setOrganization($dto->organization);

        if($dto->issueDate){
            $this->setIssueDate(new \DateTime($dto->issueDate));
        }
        if($dto->expiryDate){
            $this->setExpiryDate(new \DateTime($dto->expiryDate));
        }

        $this->setIsIndefinite($dto->isIndefinite);
        $this->setCourseUrl($dto->courseUrl);
        $this->setCourseName($dto->courseName);
        $this->setCourseAuthor($dto->courseAuthor);

        $this->setUpdatedAt(new \DateTimeImmutable());

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getOrganization(): ?string
    {
        return $this->organization;
    }

    public function setOrganization(string $organization): self
    {
        $this->organization = $organization;
        return $this;
    }

    public function getIssueDate(): ?\DateTimeInterface
    {
        return $this->issueDate;
    }

    public function setIssueDate(\DateTimeInterface $issueDate): self
    {
        $this->issueDate = $issueDate;
        return $this;
    }

    public function getExpiryDate(): ?\DateTimeInterface
    {
        return $this->expiryDate;
    }

    public function setExpiryDate(?\DateTimeInterface $expiryDate): self
    {
        $this->expiryDate = $expiryDate;
        return $this;
    }

    public function getIsIndefinite(): ?bool
    {
        return $this->isIndefinite;
    }

    public function setIsIndefinite(bool $isIndefinite): self
    {
        $this->isIndefinite = $isIndefinite;
        return $this;
    }

    public function getCourseUrl(): ?string
    {
        return $this->courseUrl;
    }

    public function setCourseUrl(?string $courseUrl): self
    {
        $this->courseUrl = $courseUrl;
        return $this;
    }

    public function getCourseName(): ?string
    {
        return $this->courseName;
    }

    public function setCourseName(?string $courseName): self
    {
        $this->courseName = $courseName;
        return $this;
    }

    public function getCourseAuthor(): ?string
    {
        return $this->courseAuthor;
    }

    public function setCourseAuthor(string $courseAuthor): self
    {
        $this->courseAuthor = $courseAuthor;
        return $this;
    }

    public function getCertificateFile(): ?string
    {
        return $this->certificateFile;
    }

    public function setCertificateFile(?string $certificateFile): self
    {
        $this->certificateFile = $certificateFile;
        return $this;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): self
    {
        $this->userName = $userName;
        return $this;
    }

    public function setFile(?File $file = null): void
    {
        $this->file = $file;

        if (null !== $file) {
            $this->setUpdatedAt(new \DateTimeImmutable());
        }
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getStatus(): ?string
    {
        $today = new \DateTime();
        $expiryDate = $this->expiryDate;

        if($this->status === CertificateStatus::STATUS_REVIEW->value){
            return CertificateStatus::STATUS_REVIEW->value;
        }

        if($expiryDate > $today || $this->isIndefinite){
            return CertificateStatus::STATUS_ACTIVE->value;
        }

        return CertificateStatus::STATUS_INACTIVE->value;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getCourseSource(): ?string
    {
        return $this->courseSource;
    }

    public function setCourseSource(string $courseSource): self
    {
        $this->courseSource = $courseSource;
        return $this;
    }

    public function isIsIndefinite(): ?bool
    {
        return $this->isIndefinite;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): static
    {
        $this->filePath = $filePath;

        return $this;
    }
}
