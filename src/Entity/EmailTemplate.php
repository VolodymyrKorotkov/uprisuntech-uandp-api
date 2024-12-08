<?php

namespace App\Entity;

use App\Enum\EmailTemplateUseInEnum;
use App\Repository\EmailTemplateRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: EmailTemplateRepository::class)]
class EmailTemplate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[NotBlank]
    #[ORM\Column(type: 'string')]
    private string|null $subject;

    #[NotBlank]
    #[ORM\Column(type: 'text')]
    private string|null $message;

    #[NotBlank]
    #[ORM\Column(enumType: EmailTemplateUseInEnum::class)]
    private EmailTemplateUseInEnum|null $useIn;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): void
    {
        $this->subject = $subject;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    public function getUseIn(): ?EmailTemplateUseInEnum
    {
        return $this->useIn;
    }

    public function setUseIn(?EmailTemplateUseInEnum $useIn): void
    {
        $this->useIn = $useIn;
    }
}
