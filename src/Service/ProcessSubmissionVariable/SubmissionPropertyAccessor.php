<?php declare(strict_types=1);

namespace App\Service\ProcessSubmissionVariable;

use App\Entity\FormIo;
use App\Serializer\AppJsonNormalizerCopyInterface;
use App\Service\FormIoClient\Dto\FormSubmission\FormSubmissionDto;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

final readonly class SubmissionPropertyAccessor
{
    private PropertyAccessor $propertyAccessor;

    public function __construct(
        private AppJsonNormalizerCopyInterface $appJsonNormalizer
    )
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * @throws PropertyValueIsEmptyException
     */
    public function getApplicationNumber(FormSubmissionDto $submission, FormIo $formIo): string
    {
        $this->normalizeSubmissionData($submission);

        return (string)$this->propertyAccessor->getValue(
            objectOrArray: $submission->data,
            propertyPath: $formIo->getApplicationNumberPropertyPath()
        ) ?? throw new PropertyValueIsEmptyException('App number is empty');
    }

    public function applicationNumberIsConfigured(FormIo $formIo): bool
    {
        return !empty($formIo->getApplicationNumberPropertyPath());
    }

    /**
     * @throws PropertyValueIsEmptyException
     */
    public function getAddress(FormSubmissionDto $submission, FormIo $formIo): array
    {
        $this->normalizeSubmissionData($submission);

        return $this->propertyAccessor->getValue(
            objectOrArray: $submission->data,
            propertyPath: $formIo->getAddressPropertyPath() ?? throw new PropertyValueIsEmptyException('Address is empty')
        ) ?? throw new PropertyValueIsEmptyException('Address is empty');
    }

    /**
     * @throws PropertyValueIsEmptyException
     */
    public function getZipCode(FormSubmissionDto $submission, FormIo $formIo): string
    {
        $this->normalizeSubmissionData($submission);

        return $this->propertyAccessor->getValue(
            objectOrArray: $submission->data,
            propertyPath: $formIo->getZipCodePropertyPath() ?? throw new PropertyValueIsEmptyException('Zip code is empty')
        ) ?? throw new PropertyValueIsEmptyException('Zip code is empty');
    }

    /**
     * @throws PropertyValueIsEmptyException
     */
    public function getApplicationEmail(FormSubmissionDto $submission, FormIo $formIo): string
    {
        $this->normalizeSubmissionData($submission);

        return $this->propertyAccessor->getValue(
            objectOrArray: $submission->data,
            propertyPath: $formIo->getEmailPropertyPath()
        ) ?? throw new PropertyValueIsEmptyException('Email is empty');
    }

    /**
     * @throws PropertyValueIsEmptyException
     */
    public function getApplicationFirstName(FormSubmissionDto $submission, FormIo $formIo): string
    {
        $this->normalizeSubmissionData($submission);

        return $this->propertyAccessor->getValue(
            objectOrArray: $submission->data,
            propertyPath: $formIo->getFirstNamePropertyPath()
        ) ?? throw new PropertyValueIsEmptyException('Name is empty');
    }

    /**
     * @throws PropertyValueIsEmptyException
     */
    public function getApplicationLastName(FormSubmissionDto $submission, FormIo $formIo): string
    {
        $this->normalizeSubmissionData($submission);

        return $this->propertyAccessor->getValue(
            objectOrArray: $submission->data,
            propertyPath: $formIo->getLastNamePropertyPath()
        ) ?? throw new PropertyValueIsEmptyException('Last name is empty');
    }

    public function setApplicationNumber(FormSubmissionDto $submission, FormIo $formIo, string $number): void
    {
        $this->normalizeSubmissionData($submission);

        $this->propertyAccessor->setValue(
            objectOrArray: $submission->data,
            propertyPath: $formIo->getApplicationNumberPropertyPath(),
            value: $number
        );
    }

    /**
     * @throws PropertyValueIsEmptyException
     */
    public function getStatus(FormSubmissionDto $submission, FormIo $formIo): string|bool|int|float
    {
        if (!$formIo->getStatusPath()){
            throw new PropertyValueIsEmptyException('Status is empty');
        }

        $submissionNormal = $this->normalizeSubmission($submission);

        return $this->propertyAccessor->getValue(
            objectOrArray: $submissionNormal,
            propertyPath: $formIo->getStatusPath()
        ) ?? throw new PropertyValueIsEmptyException('Status is empty');
    }

    public function setStatusValue(FormSubmissionDto $submission, FormIo $formIo, mixed $value): FormSubmissionDto
    {
        $this->normalizeSubmissionData($submission);

        $this->propertyAccessor->setValue(
            objectOrArray: $submission->data,
            propertyPath: $formIo->getStatusPath(),
            value: $value
        );

        return $submission;
    }

    public function getStatusConfirmValue(FormIo $formIo): mixed
    {
        $status = $formIo->getConfirmedStatusValue() ?? throw new PropertyValueIsEmptyException('Status confirm value is empty');

        if ($status === 'true'){
            return true;
        }

        if ($status === 'false'){
            return false;
        }

        return $status;
    }

    /**
     * @throws PropertyValueIsEmptyException
     */
    public function getStatusDraftValue(FormIo $formIo): mixed
    {
        $status = $formIo->getDraftStatusValue() ?? throw new PropertyValueIsEmptyException('Status draft value is empty');

        if ($status === 'true'){
            return true;
        }

        if ($status === 'false'){
            return false;
        }

        return $status;
    }

    private function normalizeSubmissionData(FormSubmissionDto|array $submission): void
    {
        $submission->data = $this->appJsonNormalizer->normalize($submission->data);
    }

    private function normalizeSubmission(FormSubmissionDto|array $submission): array
    {
        return $this->appJsonNormalizer->normalize($submission);
    }
}
