<?php declare(strict_types=1);

namespace App\Service\AggregateProcessStarter;

use Symfony\Component\Validator\Constraints\NotBlank;

final class AggregateProcessTaskTypeDto
{
    #[NotBlank]
    public int|null $id;
}