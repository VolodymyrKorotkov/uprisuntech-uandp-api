<?php declare(strict_types=1);

namespace App\Service\FormIoClient\Dto\FormMetadata;

use App\Service\FormIoClient\Dto\AbstractFormIoResponse;
use Symfony\Component\Serializer\Annotation\SerializedName;

final class FormMetadataDto extends AbstractFormIoResponse
{
    #[SerializedName('_id')]
    public string|null $id = null;
    public ?string $title = null;
    public ?string $name = null;
    public ?string $path = null;
    public ?string $type = null;
    public ?string $display = null;
    public ?array $tags = [];
    /** @var FormAccessDto[]|null */
    public ?array $access = [];
    /** @var FormSubmissionAccessDto[]|null */
    public ?array $submissionAccess = [];
    public ?string $owner = null;
//    /** @var FormComponentsDto[]|null */
    public ?array $components=[];
    public ?array $settings=[];
    public ?array $properties=[];
    public ?string $project = null;
    public ?string $controller = null;
    public ?string $revisions = null;
    public ?string $submissionRevisions = null;
    public ?int $vid = null;
    public ?string $created = null;
    public ?string $modified = null;
    public ?string $machineName = null;
    public ?FormFieldMatchAccess $fieldMatchAccess = null;
    public ?string $plan = null;
}
