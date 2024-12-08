<?php declare(strict_types=1);

namespace App\Service\FormIoClient\Dto;

use App\Service\FormIoClient\Dto\FormSubmission\FormSubmissionDto;

final readonly class CreateSubmissionDto
{
    public function __construct(
        public string $formKey,
        public FormSubmissionDto  $data
    )
    {
    }
}
