<?php declare(strict_types=1);

namespace App\Service\FormIoClient\Dto;

final readonly class GetSubmissionDto
{
    public function __construct(
        public string $formKey,
        public string|null $submissionId,
    )
    {
    }
}