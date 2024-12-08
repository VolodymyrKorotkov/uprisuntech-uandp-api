<?php declare(strict_types=1);

namespace App\Service\AggregateProcessStarter;

use App\Entity\FormIo;

final readonly class GetProcessSubmissionIdResult
{
    public function __construct(
        public FormIo $formIo,
        public string $submissionId
    )
    {
    }
}