<?php declare(strict_types=1);

namespace App\Service\FormioSubmissionSaveHandler;

use App\Entity\FormIo;
use App\Service\FormIoClient\Dto\FormSubmission\FormSubmissionDto;
use App\Service\ProcessSubmissionVariable\PropertyValueIsEmptyException;
use App\Service\ProcessSubmissionVariable\SubmissionPropertyAccessor;

final readonly class SubmissionStatusChecker
{
    public function __construct(
        private SubmissionPropertyAccessor $submissionPropertyAccessor
    )
    {
    }

    public function isConfirmedStatus(FormSubmissionDto $submission, FormIo $formIo): bool
    {
        try {
            $status = $this->submissionPropertyAccessor->getStatus($submission, $formIo);
            //submitted
            $statusConfirmValue = $this->submissionPropertyAccessor->getStatusConfirmValue($formIo);
        } catch (PropertyValueIsEmptyException $e) {
            return false;
        }
//        $statusType = gettype($status);
//        if ($statusType === 'boolean') {
//            $statusConfirmValue = $statusConfirmValue === 'true';
//        } else {
//            settype($statusConfirmValue, gettype($status));
//        }

        return $status === $statusConfirmValue;
    }
}
