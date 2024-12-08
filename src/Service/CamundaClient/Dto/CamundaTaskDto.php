<?php

namespace App\Service\CamundaClient\Dto;

class CamundaTaskDto
{
    public ?string $id = null;
    private ?string $formKey = null;
    public ?string $tenantId = null;
    public ?string $camundaFormRef = null;
    public ?bool $suspended = null;
    public ?string $caseDefinitionId = null;
    public ?string $caseInstanceId = null;
    public ?string $caseExecutionId = null;
    public ?string $taskDefinitionKey = null;
    public ?string $processInstanceId = null;
    public ?string $processDefinitionId = null;
    public ?int $priority = null;
    public ?string $parentTaskId = null;
    public ?string $owner = null;
    public ?string $executionId = null;
    public ?string $description = null;
    public ?string $delegationState = null;
    public ?string $followUp = null;
    public ?string $due = null;
    public ?string $created = null;
    public ?string $assignee = null;
    public ?string $name = null;

    public function __construct(string|null $id, string|null $formKey)
    {
        $this->id = $id;
        $this->formKey = $formKey;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFormKeyLowerCase(): string|null
    {
        return strtolower($this->formKey);
    }

    public function getFormKey(): ?string
    {
        return $this->formKey;
    }

    public function setFormKey(?string $formKey): void
    {
        $this->formKey = $formKey;
    }
}
