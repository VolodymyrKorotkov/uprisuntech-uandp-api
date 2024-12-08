<?php

namespace App\Service\CertificateUploader\Dto;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CertificateDto
{

    public ?string $id = "";

    #[Assert\NotBlank]
    public ?string $name = "";

    #[Assert\NotBlank]
    public ?string $organization = "";

    #[Assert\NotBlank]
    public ?string $issueDate = "";

    #[Assert\NotBlank]
    public ?string $expiryDate = "";

    public ?bool $isIndefinite = false;

    #[Assert\NotBlank]
    public ?string $courseUrl = "";

    public ?string $courseName = "";

    public ?string $courseAuthor = "";

    #[Assert\NotNull]
    #[Assert\File]
    #[Assert\NotBlank]
    public ?UploadedFile $file;

    #[Assert\Callback]
    public function validateExpiryDate(ExecutionContextInterface $context, $payload)
    {
        if ($this->expiryDate) {
            $currentDate = new \DateTime();
            $expiryDate = \DateTime::createFromFormat('Y-m-d', $this->expiryDate);

            if ($expiryDate < $currentDate) {
                $context->buildViolation('The expiry date must be greater than or equal to the current date.')
                    ->atPath('expiryDate')
                    ->addViolation();
            }
        }
    }

    #[Assert\Callback]
    public function validateIssueDate(ExecutionContextInterface $context, $payload)
    {
        if ($this->issueDate) {
            $currentDate = new \DateTime();
            $issueDate = \DateTime::createFromFormat('Y-m-d', $this->issueDate);

            if ($issueDate > $currentDate) {
                $context->buildViolation('The issue date must be less than or equal to the current date.')
                    ->atPath('issueDate')
                    ->addViolation();
            }
        }
    }

    public static function mapToDto(Request $request, int $id = null): self
    {
        $dto = new self;
        $dto->id = $id;
        $dto->name = $request->get('name') ?? "";
        $dto->organization = $request->get('organization') ?? "";
        $dto->issueDate = $request->get('issueDate') ?? "";
        $dto->expiryDate = $request->get('expiryDate') ?? "";
        $dto->isIndefinite = self::interpretIsIndefinite($request->get('isIndefinite'));
        $dto->courseUrl = $request->get('courseUrl') ?? "";
        $dto->courseName = $request->get('courseName') ?? "";
        $dto->courseAuthor = $request->get('courseAuthor') ?? "";
        $dto->file = $request->files->get('file');

        return $dto;
    }

    private static function interpretIsIndefinite($value): bool
    {
        return $value === '1';
    }


}
