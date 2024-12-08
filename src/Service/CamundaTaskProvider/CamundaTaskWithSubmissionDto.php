<?php declare(strict_types=1);

namespace App\Service\CamundaTaskProvider;

use App\Service\AggregateProcessStarter\GetProcessSubmissionIdResult;
use App\Service\CamundaClient\Dto\CamundaTaskDto;

final readonly class CamundaTaskWithSubmissionDto
{
    public function __construct(
        public CamundaTaskDto        $camundaTask,
        public GetProcessSubmissionIdResult $processSubmission
    )
    {
    }
}
