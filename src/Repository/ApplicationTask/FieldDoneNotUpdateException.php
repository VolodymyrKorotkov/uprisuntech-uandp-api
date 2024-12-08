<?php declare(strict_types=1);

namespace App\Repository\ApplicationTask;

use Throwable;

final class FieldDoneNotUpdateException extends \Exception
{
    public function __construct(int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('Field "done" was not to update', $code, $previous);
    }
}