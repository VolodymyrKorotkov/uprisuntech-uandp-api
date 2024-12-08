<?php declare(strict_types=1);

namespace App\Service\CamundaClient\Dto;

use App\Entity\CamundaStrategy;

final readonly class StartProcessDto
{
    public function __construct(
        public string $processAlias,
        public string $tenantId,
        public array  $variables = []
    )
    {
    }

    public static function newFromApplicationType(CamundaStrategy $camundaFlow): StartProcessDto
    {
        return new self(
            processAlias: $camundaFlow->getCamundaAlias(),
            tenantId: $camundaFlow->getTenantId()
        );
    }
}
