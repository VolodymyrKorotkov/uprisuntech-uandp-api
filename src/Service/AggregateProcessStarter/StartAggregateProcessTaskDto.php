<?php declare(strict_types=1);

namespace App\Service\AggregateProcessStarter;

use App\Service\FormIoClient\Dto\FormSubmission\FormSubmissionDto;
use Symfony\Component\Validator\Constraints\NotBlank;

final class StartAggregateProcessTaskDto
{
    #[NotBlank]
    public AggregateProcessTaskTypeDto|null $type;

    public FormSubmissionDto|null $submission = null;
    public string|null $title = null;

    public function setTypeId(int $typeID): self
    {
        $this->type = new AggregateProcessTaskTypeDto();
        $this->type->id = $typeID;

        return $this;
    }
}
