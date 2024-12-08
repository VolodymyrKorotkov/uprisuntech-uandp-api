<?php declare(strict_types=1);

namespace App\Service\FormIoClient\Dto\FormMetadata;

final class FormFieldMatchAccess
{
    /** @var FormReadDto[]|null */
    public ?array $read;
    /** @var FormWriteDto[]|null */
    public ?array $write;
    /** @var FormCreateDto[]|null */
    public ?array $create;
    /** @var FormAdminDto[]|null */
    public ?array $admin;
    /** @var FormDeleteDto[]|null */
    public ?array $delete;
    /** @var FormUpdateDto[]|null */
    public ?array $update;
    public ?string $id;
}