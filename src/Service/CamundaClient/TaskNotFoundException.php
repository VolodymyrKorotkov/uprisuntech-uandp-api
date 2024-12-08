<?php declare(strict_types=1);

namespace App\Service\CamundaClient;

use App\ApplicationFlow\Service\CamundaClient\Throwable;

final class TaskNotFoundException extends \Exception
{
    public function __construct(int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('Task not found', $code, $previous);
    }
}
