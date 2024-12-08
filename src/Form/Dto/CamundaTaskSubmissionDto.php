<?php declare(strict_types=1);

namespace App\Form\Dto;

use App\Entity\FormProcessSubmission;
use App\Service\CamundaClient\Dto\CamundaTaskDto;

final class CamundaTaskSubmissionDto
{
    public function __construct(
        public CamundaTaskDto        $task,
        public FormProcessSubmission $submission,
    )
    {
    }
}