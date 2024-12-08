<?php declare(strict_types=1);

namespace App\Service\ProcessSubmissionCollectionProvider;

use App\Entity\FormProcessSubmission;
use App\Service\FormIoClient\Dto\FormSubmission\FormSubmissionDto;
use Traversable;

final class ProcessSubmissionCollection implements \IteratorAggregate
{
    /**
     * @var array<FormSubmissionDto>
     */
    private array $submissions;

    /**
     * @var array<FormProcessSubmission>
     */
    private array $formProcessSubmissions;

    public function addSubmission(FormSubmissionDto $submissionDto, FormProcessSubmission $formProcessSubmission): void
    {
        $this->submissions[$submissionDto->id] = $submissionDto;
        $this->formProcessSubmissions[$formProcessSubmission->getSubmissionId()] = $formProcessSubmission;
    }

    public function getSubmission(string $submissionId): FormSubmissionDto
    {
        return $this->submissions[$submissionId];
    }

    public function getFormProcessSubmission(string $submissionId): FormProcessSubmission
    {
        return $this->formProcessSubmissions[$submissionId];
    }

    /**
     * @return Traversable<FormSubmissionDto>|FormSubmissionDto[]
     */
    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->submissions);
    }
}