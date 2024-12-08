<?php

namespace App\Service\ProcessSubmissionIdLinker\Dto;

use App\Entity\FormIo;
use App\Service\FormIoClient\Dto\FormSubmission\FormSubmissionDto;

final readonly class LinkSubmissionWithTaskDto
{
    public function __construct(
        public string $processInstanceId,
        public FormIo $formIo,
        public FormSubmissionDto $dto,
        public string $taskId
    )
    {
    }
}