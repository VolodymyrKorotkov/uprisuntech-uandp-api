<?php declare(strict_types=1);

namespace App\Service\ProcessSubmissionIdLinker\Dto;

use App\Entity\FormIo;

final readonly class GetSubmissionIdForProcessDto
{
    public function __construct(
        public string $processInstanceId,
        public FormIo $formio
    )
    {
    }
}
