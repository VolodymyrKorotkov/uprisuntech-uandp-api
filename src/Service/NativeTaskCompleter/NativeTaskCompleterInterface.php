<?php

namespace App\Service\NativeTaskCompleter;

use App\Entity\ApplicationTask;

interface NativeTaskCompleterInterface
{
    public function completeTask(ApplicationTask $applicationTask): void;
}
