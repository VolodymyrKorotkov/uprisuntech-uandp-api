<?php declare(strict_types=1);

namespace App\Service\FormIoClient\Dto\FormMetadata;

final class NestedFormComponentDto
{
    public string|null $label;
    public string|null $form;
    public string|null $key;
    public string|null $type;
    public bool|null $tableView;
    public bool|null $input;
    public bool|null $useOriginalRevision;
}
