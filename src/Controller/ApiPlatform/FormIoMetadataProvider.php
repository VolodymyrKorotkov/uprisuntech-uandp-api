<?php

namespace App\Controller\ApiPlatform;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\FormIo;
use App\Service\FormIoClient\Dto\FormMetadata\FormMetadataDto;
use App\Service\FormIoClient\Dto\GetFormDto;
use App\Service\FormIoClient\FormIoClient;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class FormIoMetadataProvider implements ProviderInterface
{
    public function __construct(
        private FormIoClient      $formIoClient,
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private ProviderInterface $itemProvider,
    )
    {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): FormMetadataDto
    {
        return $this->formIoClient->getForm(
            new GetFormDto(
                formKey: $this->getForm($operation, $uriVariables, $context)->getFormKey()
            )
        );
    }

    private function getForm(Operation $operation, array $uriVariables, array $context): FormIo
    {
        return $this->itemProvider->provide($operation, $uriVariables, $context);
    }
}
