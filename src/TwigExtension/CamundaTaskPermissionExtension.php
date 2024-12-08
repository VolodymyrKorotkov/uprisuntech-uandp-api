<?php declare(strict_types=1);

namespace App\TwigExtension;

use App\Service\CamundaClient\Dto\CamundaTaskDto;
use App\Service\CamundaTaskPermission;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class CamundaTaskPermissionExtension extends AbstractExtension
{
    public function __construct(
        private readonly CamundaTaskPermission $camundaTaskPermission
    )
    {
    }

    public function getFunctions(): array|\Generator
    {
        yield new TwigFunction(
            name: 'canAssignCamundaTask',
            callable: fn(CamundaTaskDto $task) => $this->camundaTaskPermission->canAssign($task)
        );
        yield new TwigFunction(
            name: 'canCompleteCamundaTask',
            callable: fn(CamundaTaskDto $task) => $this->camundaTaskPermission->canComplete($task)
        );
        yield new TwigFunction(
            name: 'lockForUpdateCamundaTask',
            callable: fn(CamundaTaskDto $task) => $this->camundaTaskPermission->lockForUpdate($task)
        );
    }
}
