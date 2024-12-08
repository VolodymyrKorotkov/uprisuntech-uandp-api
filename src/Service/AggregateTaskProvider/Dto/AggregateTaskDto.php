<?php declare(strict_types=1);

namespace App\Service\AggregateTaskProvider\Dto;

use App\Entity\ApplicationTask;
use App\Entity\ApplicationType;
use App\Entity\FormIo;
use App\Serializer\SerializerGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;

final class AggregateTaskDto
{
    #[Groups([SerializerGroupsEnum::SAFE_VIEW, ApplicationTask::UPDATE_GROUP])]
    public string|null $id = null;

    #[Groups([SerializerGroupsEnum::SAFE_VIEW, ApplicationTask::UPDATE_GROUP])]
    public string|null $processId = null;

    #[Groups(SerializerGroupsEnum::SAFE_VIEW)]
    public \DateTimeInterface|null $updatedAt = null;

    #[Groups(SerializerGroupsEnum::SAFE_VIEW)]
    public \DateTimeInterface|null $createdAt = null;

    #[Groups([SerializerGroupsEnum::SAFE_VIEW])]
    public ApplicationType|null $type;

    #[Groups(SerializerGroupsEnum::SAFE_VIEW)]
    public bool $lockForUpdate = false;

    #[Groups(SerializerGroupsEnum::SAFE_VIEW)]
    public bool $belongsOrganization = false;

    #[Groups(SerializerGroupsEnum::SAFE_VIEW)]
    public FormIo|null $form = null;

    #[Groups(SerializerGroupsEnum::SAFE_VIEW)]
    public bool|null $processed = false;

    #[Groups(SerializerGroupsEnum::SAFE_VIEW)]
    public bool|null $draft = false;

    #[Groups([SerializerGroupsEnum::SAFE_VIEW, ApplicationTask::CREATE_GROUP])]
    public string|null $submissionId = null;

    #[Groups(SerializerGroupsEnum::SAFE_VIEW)]
    public array $submission = [];

    #[Groups(SerializerGroupsEnum::SAFE_VIEW)]
    public string $title = '';

    #[Groups(SerializerGroupsEnum::SAFE_VIEW)]
    public bool $needAssign = true;
}
