<?php declare(strict_types=1);

namespace App\Controller\ApiPlatform\Dto;

use App\Service\ProcessSubmissionTableProvider\FormTableComponentDto;

final readonly class SubmissionsAsTableWithStartForm
{
    public function __construct(
        /**
         * @var array<FormTableComponentDto>
         */
        public array       $components = [],
        public array       $submissionValues = [],
        public string|null $startSubmissionId = null,
        public string|null $startFormKey = null,
        public array       $startSubmission = []
    )
    {
    }
}
