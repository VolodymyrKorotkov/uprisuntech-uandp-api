<?php

namespace App\Service\CamundaTaskProvider;

interface CamundaTaskProviderInterface
{
    public function getTaskWithProcessSubmission(string $taskId): CamundaTaskWithSubmissionDto;
}
