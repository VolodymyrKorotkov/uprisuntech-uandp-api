<?php declare(strict_types=1);

namespace App\Service\FormIoClient\Dto\FormSubmission;

final class FormMetadataDto
{
    public ?string $timezone;
    public ?int $offset;
    public ?string $origin;
    public ?string $referrer;
    public ?string $browserName;
    public ?string $userAgent;
    public ?string $pathName;
    public ?bool $onLine;
}
