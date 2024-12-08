<?php declare(strict_types=1);

namespace App\Repository\FormProcessSubmission;

use App\Entity\FormIo;
use App\Entity\FormProcessSubmission;

final readonly class FormProcessSubmissionCollection implements \IteratorAggregate
{
    /**
     * @param array<FormProcessSubmission> $items
     */
    public function __construct(
        private array $items
    )
    {
    }

    public function getSubmissionIds(): array
    {
        return array_map(
            fn(FormProcessSubmission $submission) => $submission->getSubmissionId(),
            $this->items
        );
    }

    public function hasByForm(FormIo $form): bool
    {
        foreach ($this->items as $processSubmission){
            if ($processSubmission->getForm() === $form){
                return true;
            }
        }

        return false;
    }

    /**
     * @throws \Exception
     */
    public function getByForm(FormIo $form): FormProcessSubmission
    {
        foreach ($this->items as $processSubmission){
            if ($processSubmission->getForm() === $form){
                return $processSubmission;
            }
        }

        throw new \Exception('Submission not found for form '.$form->getFormKey());
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->items);
    }
}
