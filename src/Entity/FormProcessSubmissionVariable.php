<?php declare(strict_types=1);

namespace App\Entity;

use App\Repository\FormProcessSubmissionVariable\FormProcessSubmissionVariableRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[UniqueEntity('submissionId')]
#[UniqueEntity(fields: ['key', 'processInstanceId'])]
#[ORM\Entity(repositoryClass: FormProcessSubmissionVariableRepository::class)]
class FormProcessSubmissionVariable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    private string $key;

    #[ORM\Column]
    private string $submissionProperty;

    #[ORM\Column]
    private string $processInstanceId;

    #[ORM\Column]
    private string $submissionId;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    public function getProcessInstanceId(): string
    {
        return $this->processInstanceId;
    }

    public function setProcessInstanceId(string $processInstanceId): void
    {
        $this->processInstanceId = $processInstanceId;
    }

    public function getSubmissionId(): string
    {
        return $this->submissionId;
    }

    public function setSubmissionId(string $submissionId): void
    {
        $this->submissionId = $submissionId;
    }

    public function getSubmissionProperty(): string
    {
        return $this->submissionProperty;
    }

    public function setSubmissionProperty(string $submissionProperty): void
    {
        $this->submissionProperty = $submissionProperty;
    }
}
