<?php declare(strict_types=1);

namespace App\TwigExtension;

use App\Service\FormSubmissionEditLockerService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class SubmissionLockerExtension extends AbstractExtension
{
    public function __construct(
        private readonly FormSubmissionEditLockerService $lockerService
    )
    {
    }

    public function getFunctions(): array|\Generator
    {
        yield new TwigFunction(
            name: 'isLockedSubmissionEdit',
            callable: fn(string $submissionId) => $this->lockerService->isLocked($submissionId)
        );
    }
}
