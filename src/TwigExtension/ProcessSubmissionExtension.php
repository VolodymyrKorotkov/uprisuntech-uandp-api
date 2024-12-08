<?php declare(strict_types=1);

namespace App\TwigExtension;

use App\Entity\ApplicationTask;
use App\Service\ProcessSubmissionIdLinker\Dto\GetSubmissionIdForTaskDto;
use App\Service\ProcessSubmissionIdLinker\ProcessSubmissionIdLinker;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ProcessSubmissionExtension extends AbstractExtension
{
    public function __construct(
        private readonly ProcessSubmissionIdLinker $processSubmissionIdLinker
    )
    {
    }

    public function getFunctions(): array|\Generator
    {
        yield new TwigFunction(
            name: 'getNativeTaskProcessSubmissionId',
            callable: fn(ApplicationTask $task) => $this->processSubmissionIdLinker->getSubmissionIdForTask(
                GetSubmissionIdForTaskDto::newFromNativeTask($task)
            )
        );
    }
}
