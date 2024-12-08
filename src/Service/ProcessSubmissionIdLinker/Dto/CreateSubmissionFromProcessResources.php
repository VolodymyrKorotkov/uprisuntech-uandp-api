<?php declare(strict_types=1);

namespace App\Service\ProcessSubmissionIdLinker\Dto;

use App\Entity\FormIo;

final readonly class CreateSubmissionFromProcessResources
{
    public function __construct(
        public string $processInstanceId,
        public FormIo $formIo
    )
    {
    }

    public static function newFromGetSubmissionIdForProcess(GetSubmissionIdForProcessDto $dto): CreateSubmissionFromProcessResources
    {
        return new self(
            processInstanceId: $dto->processInstanceId,
            formIo: $dto->formio
        );
    }

    public static function newFromGetSubmissionIdForTask(GetSubmissionIdForTaskDto $dto): CreateSubmissionFromProcessResources
    {
        return new self(
            processInstanceId: $dto->processInstanceId,
            formIo: $dto->formio
        );
    }
}
