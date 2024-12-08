<?php declare(strict_types=1);

namespace App\Entity;

use App\Repository\InstallerEmailRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Email;

#[ORM\Entity(repositoryClass: InstallerEmailRepository::class)]
#[UniqueEntity(fields: ['email'])]
#[UniqueEntity(fields: ['title'])]
class InstallerEmail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Email]
    private ?string $email = null;

    #[ORM\Column]
    private ?string $title = null;

    #[ORM\Column(nullable: true)]
    private ?string $stateShort = null;

    #[ORM\Column(nullable: true)]
    private ?int $zipCodeMin = null;

    #[ORM\Column(nullable: true)]
    private ?int $zipCodeMax = null;

    #[ORM\Column]
    private ?string $callBackUrl = 'https://stage-api.uprisun.dev:3000/formio/manage/view/#/form/managerproposaluser/submission/{submissionId}/edit';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getCallBackUrl(): ?string
    {
        return $this->callBackUrl;
    }

    public function setCallBackUrl(?string $callBackUrl): void
    {
        $this->callBackUrl = $callBackUrl;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getStateShort(): ?string
    {
        return $this->stateShort;
    }

    public function setStateShort(?string $stateShort): void
    {
        $this->stateShort = $stateShort;
    }

    public function getZipCodeMin(): ?int
    {
        return $this->zipCodeMin;
    }

    public function setZipCodeMin(?int $zipCodeMin): void
    {
        $this->zipCodeMin = $zipCodeMin;
    }

    public function getZipCodeMax(): ?int
    {
        return $this->zipCodeMax;
    }

    public function setZipCodeMax(?int $zipCodeMax): void
    {
        $this->zipCodeMax = $zipCodeMax;
    }
}
