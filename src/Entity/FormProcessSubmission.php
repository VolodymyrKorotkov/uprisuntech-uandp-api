<?php declare(strict_types=1);

namespace App\Entity;

use App\Repository\FormProcessSubmission\FormProcessSubmissionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: FormProcessSubmissionRepository::class)]
#[UniqueEntity(fields: ['processInstanceId', 'form'])]
#[UniqueEntity(fields: ['submissionId'])]
class FormProcessSubmission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private FormIo|null $form = null;

    #[ORM\Column]
    private string|null $processInstanceId = null;

    #[ORM\Column(nullable: true)]
    private string|null $submissionId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getForm(): ?FormIo
    {
        return $this->form;
    }

    public function setForm(?FormIo $form): void
    {
        $this->form = $form;
    }

    public function getSubmissionId(): ?string
    {
        return $this->submissionId;
    }

    public function hasSubmissionId(): bool
    {
        return null !== $this->submissionId;
    }

    public function setSubmissionId(?string $submissionId): void
    {
        $this->submissionId = $submissionId;
    }

    public function getProcessInstanceId(): ?string
    {
        return $this->processInstanceId;
    }

    public function setProcessInstanceId(?string $processInstanceId): void
    {
        $this->processInstanceId = $processInstanceId;
    }
}
