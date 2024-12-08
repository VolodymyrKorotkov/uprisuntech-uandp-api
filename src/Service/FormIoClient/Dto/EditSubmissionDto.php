<?php declare(strict_types=1);

namespace App\Service\FormIoClient\Dto;

use App\Service\FormIoClient\Dto\FormSubmission\FormSubmissionDto;

final readonly class EditSubmissionDto
{
    public function __construct(
        public string $formKey,
        public string $submissionId,
        public FormSubmissionDto  $data
    )
    {
    }
}
