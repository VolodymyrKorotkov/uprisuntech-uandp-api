<?php declare(strict_types=1);

namespace App\Service\FormioSubmissionSaveHandler\Dto;

use App\Service\FormIoClient\Dto\FormSubmission\FormSubmissionDto;

final class HandleSubmissionSaveRequestDto
{
    public function __construct(
        public FormSubmissionDto|null $submission,
        public string                 $formKey
    )
    {
    }
}
