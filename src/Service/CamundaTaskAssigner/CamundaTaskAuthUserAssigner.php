<?php declare(strict_types=1);

namespace App\Service\CamundaTaskAssigner;

use App\Service\CamundaClient\Dto\CamundaTaskDto;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class CamundaTaskAuthUserAssigner
{
    public function __construct(
        private CamundaTaskAssigner $camundaTaskAssigner,
        private Security            $security
    )
    {
    }

    public function assignForAuthUser(string $taskId): CamundaTaskDto
    {
        return $this->camundaTaskAssigner->assignFor(
            $taskId,
            $this->security->getUser()->getUserIdentifier()
        );
    }
}