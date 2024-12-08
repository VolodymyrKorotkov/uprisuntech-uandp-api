<?php declare(strict_types=1);

namespace App\Service\CamundaTaskAssigner;

use App\Service\CamundaClient\CamundaClientInterface;
use App\Service\CamundaClient\Dto\CamundaTaskDto;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class CamundaTaskAssigner
{
    public function __construct(
        private CamundaClientInterface $camundaClient
    )
    {}

    public function assignFor(string $taskId, string $assignee): CamundaTaskDto
    {
        try {
            $task = $this->camundaClient->getTask($taskId);
        } catch (ClientException $clientException){
            if ($clientException->getResponse()->getStatusCode() === 404){
                throw new NotFoundHttpException('CamundaTask not found');
            }else {
                throw $clientException;
            }
        }

        $task->assignee = $assignee;
        $this->camundaClient->updateTask($task);

        return $task;
    }
}
