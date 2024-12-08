<?php declare(strict_types=1);

namespace App\Service\FormIoClient\Dto\FormMetadata;

final class FormSubmissionAccessDto
{
    public ?string $type;
    /** @var string[]|null */
    public ?array $roles;
}