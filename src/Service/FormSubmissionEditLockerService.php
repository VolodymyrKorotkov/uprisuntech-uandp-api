<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\FormSubmissionEditLocker;
use App\Repository\FormSubmissionEditLockerRepository;
use Doctrine\ORM\EntityNotFoundException;

final readonly class FormSubmissionEditLockerService
{
    public function __construct(
        private FormSubmissionEditLockerRepository $formSubmissionEditLockerRepository
    )
    {}

    public function isLocked(string $submissionID): bool
    {
        try {
            return $this->formSubmissionEditLockerRepository->getBySubmissionId($submissionID)->isLocked();
        } catch (EntityNotFoundException) {
           return false;
        }
    }

    public function lockSubmissionForEdit(string $submissionID): void
    {
        $this->setLock($submissionID, true);
    }

    public function unlockSubmissionForEdit(string $submissionID): void
    {
        $this->setLock($submissionID, false);
    }

    private function setLock(string $submissionID, bool $lock): void
    {
        try {
            $lockEntity = $this->formSubmissionEditLockerRepository->getBySubmissionId($submissionID);
        } catch (EntityNotFoundException) {
            $lockEntity = new FormSubmissionEditLocker;
            $lockEntity->setSubmissionId($submissionID);
        }

        $lockEntity->setLocked($lock);
        $this->formSubmissionEditLockerRepository->save($lockEntity);
    }
}
