<?php

namespace App\Service\CamundaTaskProvider;

use App\Entity\CamundaTaskFilter;
use App\Repository\ApplicationTypeRepository;
use App\Service\CamundaClient\CamundaClientInterface;
use App\Service\CamundaClient\Dto\CamundaTaskDto;
use App\Service\CamundaClient\Dto\ProcessTaskCollection;
use App\Service\CamundaClient\Dto\TaskListFilterDto;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final readonly class CamundaTaskByFilterProvider implements CamundaTaskByFilterProviderInterface
{
    public function __construct(
        private CamundaClientInterface      $camundaClient,
        private Security                    $security,
        private ApplicationTypeRepository   $applicationTypeRepository
    )
    {
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getTasks(GetTaskListFilterDto $filterDto): ProcessTaskCollection
    {
        return $this->camundaClient->getTasks(
            new TaskListFilterDto(
                $this->getTasksListParams($filterDto)
            )
        );
    }

    /**
     * @throws EntityNotFoundException
     */
    public function countTasks(GetTaskListFilterDto $filterDto): int
    {
        return $this->camundaClient->countTasks(
            new TaskListFilterDto(
                $this->getTasksListParams($filterDto)
            )
        )->count;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws NotFoundHttpException
     */
    public function getTask(string $taskId): CamundaTaskDto
    {
        try {
            return $this->camundaClient->getTask($taskId);
        } catch (ClientException $throwable) {
            if ($throwable->getResponse()->getStatusCode() === 404) {
                throw new NotFoundHttpException();
            } else {
                throw $throwable;
            }
        }
    }

    /**
     * @return array|CamundaTaskFilter[]
     * @throws EntityNotFoundException
     */
    private function getFilters(GetTaskListFilterDto $filterDto): array
    {
        return $this->applicationTypeRepository
            ->getById($filterDto->typeId)
            ->getCamundaStrategy()
            ->getFilters()
            ->toArray();
    }

    /**
     * @return string
     */
    private function getUserIdentifier(): string
    {
        return $this->security->getUser()->getUserIdentifier();
    }

    /**
     * @return string[]
     */
    private function getRoles(): array
    {
        return $this->security->getUser()->getRoles();
    }

    /**
     * @return string[]
     */
    private function getFilterVars(): array
    {
        return ['{userIdentity}', '{userRoles}'];
    }

    /**
     * @return array
     */
    private function getFilterVarsValues(): array
    {
        return [$this->getUserIdentifier(), implode(',', $this->getRoles())];
    }

    /**
     * @param GetTaskListFilterDto $filterDto
     * @return array
     * @throws EntityNotFoundException
     */
    private function getTasksListParams(GetTaskListFilterDto $filterDto): array
    {
        $params = [];
        foreach ($this->getFilters($filterDto) as $filter) {
            $val = str_replace(
                $this->getFilterVars(),
                $this->getFilterVarsValues(),
                $filter->getValue()
            );

            $params[$filter->getProperty()] = $val;
        }

        return $params;
    }
}
