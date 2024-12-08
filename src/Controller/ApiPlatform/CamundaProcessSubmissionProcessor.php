<?php declare(strict_types=1);

namespace App\Controller\ApiPlatform;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Service\ProcessSubmissionVariable\ProcessSubmissionVariableValuesSaver;

final readonly class CamundaProcessSubmissionProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessSubmissionVariableValuesSaver $valuesSaver
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $this->valuesSaver->saveProcessVariableValues($data);
    }
}
