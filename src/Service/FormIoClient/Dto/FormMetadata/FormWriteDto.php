<?php declare(strict_types=1);

namespace App\Service\FormIoClient\Dto\FormMetadata;

final class FormWriteDto
{
    public ?string $formFieldPath;
    public ?string $value;
    public ?string $operator;
    public ?string $valueType;
    public ?array $roles;
}