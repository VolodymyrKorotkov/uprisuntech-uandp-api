<?php declare(strict_types=1);

namespace App\Service\ProcessSubmissionIdLinker\Dto;

use App\Entity\FormIo;
use App\Service\FormIoClient\Dto\FormSubmission\FormSubmissionDto;

final readonly class SaveResourcesForProcessFromSubmissionDto
{
    public function __construct(
        public string $processInstanceId,
        public FormIo $formIo,
        public FormSubmissionDto $submission
    )
    {
    }

    public static function newFromLinkSubmissionWithProcess(LinkSubmissionWithProcessDto $dto): SaveResourcesForProcessFromSubmissionDto
    {
        return new self(
            processInstanceId: $dto->processInstanceId,
            formIo: $dto->formIo,
            submission: $dto->submission
        );
    }
}
