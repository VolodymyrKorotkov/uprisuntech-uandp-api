<?php

namespace App\Entity;

use App\Repository\FormSubmissionEditLockerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\NotBlank;

#[UniqueEntity(
    fields: ['submissionId']
)]
#[ORM\Entity(repositoryClass: FormSubmissionEditLockerRepository::class)]
class FormSubmissionEditLocker
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[NotBlank]
    #[ORM\Column]
    private string|null $submissionId;

    #[ORM\Column]
    private bool $locked = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubmissionId(): ?string
    {
        return $this->submissionId;
    }

    public function setSubmissionId(?string $submissionId): void
    {
        $this->submissionId = $submissionId;
    }

    public function isLocked(): bool
    {
        return $this->locked;
    }

    public function setLocked(bool $locked): void
    {
        $this->locked = $locked;
    }
}
