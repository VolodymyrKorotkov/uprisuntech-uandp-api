<?php declare(strict_types=1);

namespace App\Service\FormIoClient\Dto;

use Symfony\Component\Serializer\Annotation\SerializedName;

final class FormioProjectDto
{
    #[SerializedName('_id')]
    public string|null $id = null;
}
