<?php declare(strict_types=1);

namespace App\Entity;

use App\Repository\FormTaskSubmission\FormTaskSubmissionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: FormTaskSubmissionRepository::class)]
#[UniqueEntity(fields: ['taskId', 'form'])]
#[UniqueEntity(fields: ['processId', 'taskId'])]
#[UniqueEntity(fields: ['submissionId'])]
class FormTaskSubmission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private FormIo|null $form = null;

    #[ORM\Column(nullable: true)]
    private string|null $processId = null;

    #[ORM\Column]
    private string|null $taskId = null;

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

    public function getTaskId(): ?string
    {
        return $this->taskId;
    }

    public function setTaskId(?string $taskId): void
    {
        $this->taskId = $taskId;
    }

    public function getSubmissionId(): ?string
    {
        return $this->submissionId;
    }

    public function setSubmissionId(?string $submissionId): void
    {
        $this->submissionId = $submissionId;
    }

    public function getProcessId(): ?string
    {
        return $this->processId;
    }

    public function setProcessId(?string $processId): void
    {
        $this->processId = $processId;
    }
}
