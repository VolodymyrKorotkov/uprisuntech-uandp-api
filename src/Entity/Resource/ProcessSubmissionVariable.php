<?php declare(strict_types=1);

namespace App\Entity\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Controller\ApiPlatform\CamundaProcessSubmissionProcessor;
use App\Controller\ApiPlatform\CamundaProcessSubmissionProvider;

#[Get(
    formats: ['json'],
    provider: CamundaProcessSubmissionProvider::class
)]
#[Post(
    formats: ['json'],
    output: false,
    processor: CamundaProcessSubmissionProcessor::class
)]
final class ProcessSubmissionVariable
{
    #[ApiProperty(identifier: true)]
    public string $processInstanceId;
    public array $variables;

    public function varKeys(): array
    {
        return array_keys($this->variables);
    }

    public function getVariableValue(string $key): mixed
    {
        return $this->variables[$key];
    }
}
