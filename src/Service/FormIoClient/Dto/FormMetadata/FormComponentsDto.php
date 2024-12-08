<?php declare(strict_types=1);

namespace App\Service\FormIoClient\Dto\FormMetadata;

final class FormComponentsDto
{
    public ?string $label = null;
    public ?string $applyMaskOn = null;
    public ?bool $mask = null;
    public ?bool $tableView = null;
    public ?bool $delimiter = null;
    public ?bool $requireDecimal = null;
    public ?string $inputFormat = null;
    public ?bool $truncateMultipleSpaces = null;
    public ?string $key = null;
    public ?string $type = null;
    public ?bool $input = null;
    public ?bool $disableOnInvalid = null;
    public ?bool $autofocus = null;
    public ?bool $clearOnHide = null;
    public ?array $conditional = null;
    public mixed $defaultValue = null;
    public ?bool $disabled = false;
    public ?bool $hidden = null;
    public ?string $inputType = null;
    public ?string $labelPosition = null;
    public ?bool $persistent = null;
    public ?string $placeholder = null;
    public ?string $prefix = null;
    public ?FormComponentPropertyDto $properties = null;
    public ?bool $protected = null;
    public ?string $suffix = null;
    public ?array $tags = null;
    public ?array $validate = null;
    public ?string $title = null;
    public ?string $theme = null;

    /**
     * @var array<FormComponentsDto>
     */
    public array $components = [];
    public string|null $breadcrumb = null;
    public bool|null $hideLabel = null;
    public string|null $show = null;
    public string|null $when = null;
    public string|null $eq = null;
    public string|null $form = null;
    public bool|null $useOriginalRevision = null;
    public bool|null $reference;

    public function propertyVarKeyIsEmpty(): bool
    {
        if (null === $this->properties){
            return true;
        }

        return $this->properties->varKeyIsEmpty();
    }

    public function getPropertyVarKey(): string
    {
        return $this->properties->varKey;
    }
}
