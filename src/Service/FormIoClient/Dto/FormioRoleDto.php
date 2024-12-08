<?php declare(strict_types=1);

namespace App\Service\FormIoClient\Dto;

use Symfony\Component\Serializer\Annotation\SerializedName;

final class FormioRoleDto
{
    #[SerializedName('_id')]
    public string|null $id = null;
    public string|null $title = null;
    public string|null $description = null;
    public bool|null $default = null;
    public bool|null $admin = null;
    public string|null $project = null;
    public string|null $machineName = null;
    public string|null $created = null;
    public string|null $modified = null;
}