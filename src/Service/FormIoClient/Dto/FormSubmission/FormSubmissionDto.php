<?php

namespace App\Service\FormIoClient\Dto\FormSubmission;

use ApiPlatform\Metadata\ApiProperty;
use App\Service\FormIoClient\Dto\AbstractFormIoResponse;
use Symfony\Component\Serializer\Annotation\SerializedName;

final class FormSubmissionDto extends AbstractFormIoResponse
{
    #[SerializedName('_id')]
    public string|null $id = null;
    public string|null $form = null;
    #[ApiProperty(description: 'form data as json: "{ property: value }"')]
    public ?array $data = [];
    public ?FormMetadataDto $metadata = null;
    public ?string $state = null;
    public ?string $vnote = null;

    public ?string $created = null;
    public ?string $modified = null;
    public ?string $owner = null;
    public array $extraData = [];

    public function dataIsEmpty(): bool
    {
        return empty($this->data);
    }

    public static function newFromData(array $data): self
    {
        $res = new self();
        $res->data = $data;

        return $res;
    }

    public function replaceRecursiveData(FormSubmissionDto $dto): void
    {
        $this->data = array_replace_recursive($this->data, $dto->data);
    }

    public function asDraft(): FormSubmissionDto
    {
        $this->state = 'draft';
        return $this;
    }

    public static function newDraft(): self
    {
        return (new self())->asDraft();
    }
}
