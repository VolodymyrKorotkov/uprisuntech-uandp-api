<?php

namespace App\Service\CamundaClient;

use App\Service\CamundaClient\Dto\CamundaTaskDto;
use App\Service\CamundaClient\Dto\CompleteTaskDto;
use App\Service\CamundaClient\Dto\CountTasksResultDto;
use App\Service\CamundaClient\Dto\ProcessTaskCollection;
use App\Service\CamundaClient\Dto\StartProcessDto;
use App\Service\CamundaClient\Dto\TaskListFilterDto;
use JetBrains\PhpStorm\Deprecated;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CamundaClient implements CamundaClientInterface
{
    private const  START_PROCESS = '/engine-rest/engine/default/process-definition/key/:processAlias/tenant-id/:tenantId/start';
    private const  PROCESS_TASK_LIST = '/engine-rest/engine/default/task';
    private const  PROCESS_TASK_COUNT = '/engine-rest/engine/default/task/count';
    private const  TASK_ITEM = '/engine-rest/engine/default/task/:id';
    private const  TASK_FORM_KEY = '/engine-rest/engine/default/task/:uuid/form';
    private const  COMPLETE_TASK = '/engine-rest/engine/default/task/:uuid/complete';

    public function __construct(
        private readonly HttpClientInterface                         $httpClient,
        #[Autowire(env: 'CAMUNDA_BASE_URL')] private readonly string $camundaUrl,
        #[Autowire(env: 'CAMUNDA_USERNAME')] private readonly string $camundaUserName,
        #[Autowire(env: 'CAMUNDA_PASSWORD')] private readonly string $camundaPassword,
        private readonly SerializerInterface                         $serializer
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function startProcessAndGetId(StartProcessDto $dto): string
    {
        $response = $this->httpClient->request(
            method: 'POST',
            url: $this->getUrl(self::START_PROCESS, [
                'processAlias' => $dto->processAlias,
                'tenantId' => $dto->tenantId
            ]),
            options: [
                ...$this->getAuthOption(),
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode([
                    'variables' => $this->createCamundaVars($dto->variables)
                ])
            ]
        )->toArray();

        return $response['id'];
    }

    /**
     * Get process task list
     * @param string $processInstanceId
     * @return CamundaTaskDto
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws TaskNotFoundException
     */
    #[Deprecated('@see getTasks')]
    public function getProcessTask(string $processInstanceId): CamundaTaskDto
    {
        $response = $this->httpClient->request(
            method: 'GET',
            url: $this->getUrl(self::PROCESS_TASK_LIST),
            options: [
                ...$this->getAuthOption(),
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'query' => [
                    'processInstanceIdIn' => $processInstanceId
                ],
            ]
        );

        return $this->serializer->deserialize(
            data: $response->getContent(),
            type: CamundaTaskDto::class.'[]',
            format: 'json'
        )[0] ?? throw new TaskNotFoundException();
    }


    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getTasks(TaskListFilterDto $filterDto): ProcessTaskCollection
    {
        $response = $this->httpClient->request(
            method: 'GET',
            url: $this->getUrl(self::PROCESS_TASK_LIST),
            options: [
                ...$this->getAuthOption(),
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'query' => [
                    ...$filterDto->params,
                    'sortBy' => 'created',
                    'sortOrder' => 'desc',
                ],
            ]
        );

        return new ProcessTaskCollection(
            $this->serializer->deserialize(
                data: $response->getContent(),
                type: CamundaTaskDto::class . '[]',
                format: 'json'
            )
        );
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function countTasks(TaskListFilterDto $filterDto): CountTasksResultDto
    {
        $response = $this->httpClient->request(
            method: 'GET',
            url: $this->getUrl(self::PROCESS_TASK_COUNT),
            options: [
                ...$this->getAuthOption(),
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'query' => [
                    ...$filterDto->params,
                ],
            ]
        );

        return $this->serializer->deserialize(
            data: $response->getContent(),
            type: CountTasksResultDto::class,
            format: 'json'
        );
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getTask(string $taskId): CamundaTaskDto
    {
        $response = $this->httpClient->request(
            method: 'GET',
            url: $this->getUrl(self::TASK_ITEM, ['id' => $taskId]),
            options: [
                ...$this->getAuthOption(),
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ]
        );

        return $this->serializer->deserialize(
            data: $response->getContent(),
            type: CamundaTaskDto::class,
            format: 'json'
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function updateTask(CamundaTaskDto $taskDto): void
    {
        $this->httpClient->request(
            method: 'PUT',
            url: $this->getUrl(self::TASK_ITEM, ['id' => $taskDto->id]),
            options: [
                ...$this->getAuthOption(),
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'body' => $this->serializer->serialize($taskDto, 'json')
            ]
        );
    }

    /**
     * Get form key by task id
     * @param string $taskId
     * @return string|null
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getTaskFormKey(string $taskId): ?string
    {
        $response = $this->httpClient->request(
            method: 'GET',
            url: $this->getUrl(self::TASK_FORM_KEY, ['uuid' => $taskId]),
            options: [
                ...$this->getAuthOption(),
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
            ]
        )->toArray();

        if (!$response || !array_key_exists('key', $response)) {
            return null;
        }

        return strtolower($response['key']);
    }


    /**
     * Complete task
     * @param CompleteTaskDto $completeDto
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function completeTask(CompleteTaskDto $completeDto): void
    {
        try {
            $this->httpClient->request(
                method: 'POST',
                url: $this->getUrl(self::COMPLETE_TASK, ['uuid' => $completeDto->taskId]),
                options: [
                    ...$this->getAuthOption(),
                    'headers' => [
                        'Content-Type' => 'application/json'
                    ],
                    'body' => json_encode([
                        'variables' => $this->createCamundaVars($completeDto->variables)
                    ])
                ]
            );
        } catch (\Throwable $exception){
            throw new BadRequestHttpException($exception->getResponse()->getContent(false));
        }
    }

    private function createCamundaVars(array $vars, $result = []): array
    {
        foreach ($vars as $property => $value) {
            $type = ucfirst(gettype($value));

            if ($type === 'Array') {
                $result = $this->createCamundaVars(
                    $value['data'] ?? [],
                    $result
                );
            } else {
                $result[$property] = [
                    'value' => $value,
                    'type' => ucfirst(gettype($value))
                ];
            }
        }

        return $result;
    }

    private function getUrl(string $path, array $param = []): string
    {
        $url = $this->camundaUrl . $path;
        if ($param) {
            foreach ($param as $key => $value) {
                $url = str_replace(':' . $key, $value, $url);
            }
        }

        return $url;
    }

    private function getAuthOption(): array
    {
        return [
            'auth_basic' => [
                $this->camundaUserName,
                $this->camundaPassword
            ],
        ];
    }
}
