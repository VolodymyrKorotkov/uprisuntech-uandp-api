<?php declare(strict_types=1);

namespace App\Service\FormIoClient;

use App\Service\FormIoClient\Dto\CreateSubmissionDto;
use App\Service\FormIoClient\Dto\EditSubmissionDto;
use App\Service\FormIoClient\Dto\FormioProjectDto;
use App\Service\FormIoClient\Dto\FormioRoleDto;
use App\Service\FormIoClient\Dto\FormMetadata\FormMetadataDto;
use App\Service\FormIoClient\Dto\FormSubmission\FormSubmissionDto;
use App\Service\FormIoClient\Dto\GetFormDto;
use App\Service\FormIoClient\Dto\GetSubmissionDto;
use App\Service\HttpNativeClient\HttpNativeClient;
use App\Service\HttpNativeClient\HttpNativeResponse;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Throwable;

abstract class AbstractFormioClient
{
    protected const X_JWT_TOKEN_HEADER = 'x-jwt-token';
    protected const X_TOKEN_HEADER = 'x-token';

    public function __construct(
        private readonly HttpNativeClient $httpNativeClient
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function getForm(GetFormDto $dto): FormMetadataDto
    {
        return $this->get(path: $dto->formKey)->deserializeResponse(FormMetadataDto::class);
    }

    /**
     * @return FormMetadataDto[]
     * @throws Throwable
     */
    public function getFormsList(): array
    {
        return $this->get(path: 'form')->deserializeResponse(FormMetadataDto::class.'[]');
    }

    /**
     * @throws Throwable
     */
    public function getFormById(string $id): FormMetadataDto
    {
        $response = $this->get(
            path: 'form/{id}',
            params: ['{id}' => $id]
        );

        return $response->deserializeResponse(FormMetadataDto::class);
    }

    /**
     * @param GetSubmissionDto $dto
     * @return FormSubmissionDto
     * @throws Throwable
     */
    public function getSubmission(GetSubmissionDto $dto): FormSubmissionDto
    {
        $response = $this->get(
            path: '{formKey}/submission/{submissionId}',
            params: ['{formKey}' => $dto->formKey, '{submissionId}' => $dto->submissionId]
        );

        return $response->deserializeResponse(FormSubmissionDto::class);
    }

    /**
     * @param string $formId
     * @param string $submissionId
     * @return FormSubmissionDto
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws Throwable
     * @throws TransportExceptionInterface
     */
    public function getSubmissionByFormId(string $formId, string $submissionId): FormSubmissionDto
    {
        $response = $this->get(
            path: 'form/{formId}/submission/{submissionId}',
            params: ['{formId}' => $formId, '{submissionId}' => $submissionId]
        );

        return $response->deserializeResponse(FormSubmissionDto::class);
    }

    /**
     * @return FormSubmissionDto[]
     * @throws Throwable
     */
    public function getSubmissionList(string $formKey, array $filter = []): array
    {
        $response = $this->get(
            path: '{formKey}/submission?'.http_build_query($filter),
            params: ['{formKey}' => $formKey]
        );

        return $response->deserializeResponse(FormSubmissionDto::class.'[]');
    }

    /**
     * @throws Throwable
     */
    public function createSubmission(CreateSubmissionDto $dto): FormSubmissionDto
    {
        try {
            $response = $this->httpNativeClient->post(
                url: $this->getUrl('{formKey}/submission'),
                params: ['{formKey}' => $dto->formKey],
                data: $dto->data,
                headers: $this->getHeaders()
            );

            return $response->deserializeResponse(FormSubmissionDto::class);
        } catch (ClientException $clientException){
            throw new BadRequestException($clientException->getResponse()->getContent(false));
        }
    }

    /**
     * @param EditSubmissionDto $dto
     * @return FormSubmissionDto
     * @throws Throwable
     */
    public function editSubmission(EditSubmissionDto $dto): FormSubmissionDto
    {
        $response = $this->put(
            path: '{formKey}/submission/{submissionId}',
            params: ['{formKey}' => $dto->formKey, '{submissionId}' => $dto->submissionId],
            data: $dto->data
        );

        return $response->deserializeResponse(FormSubmissionDto::class);
    }

    /**
     * @return array<FormioRoleDto>
     * @throws Throwable
     */
    public function getRoles(): array
    {
        return $this->get('role')->deserializeResponse(FormioRoleDto::class . '[]');
    }

    /**
     * @return FormioProjectDto
     * @throws Throwable
     */
    public function getProject(): FormioProjectDto
    {
        return $this->get('')->deserializeResponse(FormioProjectDto::class);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws Throwable
     */
    private function get(string $path, array $params = []): HttpNativeResponse
    {
        return $this->httpNativeClient->get(
            url: $this->getUrl($path),
            params: $params,
            headers: $this->getHeaders()
        );
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Throwable
     */
    private function put(string $path, array $params, array|object $data = []): HttpNativeResponse
    {
        return $this->httpNativeClient->put(
            url: $this->getUrl($path),
            params: $params,
            data: $data,
            headers: $this->getHeaders()
        );
    }

    private function getUrl(string $path): string
    {
        return $this->getApiUrl() . ($path ? '/' . $path : '');
    }

    private function getHeaders(): array
    {
        if (!$this->hasToken()){
            return [];
        }

        return [
            $this->getTokenHeader() => $this->getToken()
        ];
    }

    abstract protected function getTokenHeader(): string;
    abstract protected function getToken(): ?string;
    abstract protected function getApiUrl(): string;
    abstract protected function hasToken(): bool;
}
