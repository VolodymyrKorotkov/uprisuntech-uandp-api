<?php declare(strict_types=1);

namespace App\Service\FormioSubmissionSaveHandler\Handler;

use App\Service\AggregateProcessStarter\StartAggregateProcessTaskDto;
use App\Service\AggregateProcessStarter\TaskSourcesAggregateStarterInterface;
use App\Service\FormioSubmissionSaveHandler\Dto\HandleSubmissionSaveDto;
use App\Service\FormioSubmissionSaveHandler\Dto\HandleSubmissionSaveRequestDto;
use App\Service\ProcessSubmissionVariable\SubmissionPropertyAccessor;

final readonly class StartProcessHandler implements FormioSubmissionSaveHandlerInterface
{
    public function __construct(
        private SubmissionPropertyAccessor           $submissionPropertyAccessor,
        private TaskSourcesAggregateStarterInterface $processStarter,
    )
    {
    }

    public function handle(HandleSubmissionSaveDto $dto): HandleSubmissionSaveRequestDto
    {
        $applicationType = $dto->formIo->getStartedProcessType();
        if (!$applicationType) {
            return $dto->formioWebHookRequest;
        }

        $startProcessDto = new StartAggregateProcessTaskDto();
        $startProcessDto->setTypeId($applicationType->getId());
        $startProcessDto->title = $this->getProcessTitle($dto);
        $startProcessDto->submission = $dto->formioWebHookRequest->submission;

        $this->processStarter->startAggregateProcess($startProcessDto);

        return $dto->formioWebHookRequest;
    }

    private function getProcessTitle(HandleSubmissionSaveDto $dto): ?string
    {
        if (!$this->submissionPropertyAccessor->applicationNumberIsConfigured($dto->formIo)) {
            return null;
        }

        return $this->submissionPropertyAccessor->getApplicationNumber(
                $dto->formioWebHookRequest->submission, $dto->formIo
            ) . ' ' . date('Y-m-d H:i:s');
    }
}
