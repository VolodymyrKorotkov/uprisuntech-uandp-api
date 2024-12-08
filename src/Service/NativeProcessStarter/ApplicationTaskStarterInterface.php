<?php

namespace App\Service\NativeProcessStarter;

use App\Entity\ApplicationTask;

interface ApplicationTaskStarterInterface
{
    public function startApplicationTask(ApplicationTask $task, string $processTitle): ApplicationTask;
}
