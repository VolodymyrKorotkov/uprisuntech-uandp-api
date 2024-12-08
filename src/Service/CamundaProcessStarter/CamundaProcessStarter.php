<?php declare(strict_types=1);

namespace App\Service\CamundaProcessStarter;

use App\Entity\ApplicationProcess;
use App\Entity\ApplicationType;
use App\Repository\ApplicationProcessRepository;
use App\Repository\ApplicationTypeRepository;
use App\Security\ApplicationUserSecurity;
use App\Service\CamundaClient\CamundaClientInterface;
use App\Service\CamundaClient\Dto\StartProcessDto;
use Doctrine\ORM\EntityNotFoundException;

final readonly class CamundaProcessStarter
{
    public function __construct(
        private CamundaClientInterface $camundaClient,
        private ApplicationTypeRepository $applicationTypeRepository,
        private ApplicationProcessRepository $applicationProcessRepository,
        private ApplicationUserSecurity $applicationUserSecurity,
    )
    {
    }

    /**
     * @throws EntityNotFoundException
     */
    public function startProcessInstance(int $typeId): string
    {
        $type = $this->applicationTypeRepository->getById($typeId);

        $processId = $this->camundaClient->startProcessAndGetId(new StartProcessDto(
            processAlias: $type->getCamundaStrategy()->getCamundaAlias(),
            tenantId: $type->getCamundaStrategy()->getTenantId(),
            variables: [
                'processStarterUserIdentifier' => $this->applicationUserSecurity->getUser()->getUserIdentifier()
            ]
        ));

        $this->createNewProcessOnDatabase($processId, $type);

        return $processId;
    }

    private function createNewProcessOnDatabase(string $processId, ApplicationType $type): void
    {
        $process = new ApplicationProcess();
        $process->setProcessInstanceId($processId);
        $process->setUser($this->applicationUserSecurity->getUser());
        $process->setType($type);

        $this->applicationProcessRepository->save($process);
    }
}
