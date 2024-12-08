<?php

namespace App\Entity;

use App\Repository\InstallerQuotaSubmissionIdRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\NotBlank;

#[UniqueEntity(
    fields: ['submissionId']
)]
#[ORM\Entity(repositoryClass: InstallerQuotaSubmissionIdRepository::class)]
class InstallerQuotaSubmissionId
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private InstallerEmail $installerEmail;

    #[NotBlank]
    #[ORM\Column]
    private string|null $submissionId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInstallerEmail(): InstallerEmail
    {
        return $this->installerEmail;
    }

    public function setInstallerEmail(InstallerEmail $installerEmail): void
    {
        $this->installerEmail = $installerEmail;
    }

    public function getSubmissionId(): ?string
    {
        return $this->submissionId;
    }

    public function setSubmissionId(?string $submissionId): void
    {
        $this->submissionId = $submissionId;
    }
}
