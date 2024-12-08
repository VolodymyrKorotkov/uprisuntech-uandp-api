<?php declare(strict_types=1);

namespace App\Controller\ApiPlatform;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Controller\ApiPlatform\Dto\FormioTokenDto;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

final readonly class FormIoTokenApiPlatformProvider implements ProviderInterface
{
    public function __construct(
        private RequestStack $requestStack,
    )
    {
    }

    /**
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $token = $this->requestStack->getCurrentRequest()->headers->get('Authorization');
        $token = str_replace('Bearer ', '', $token);

        return new FormioTokenDto($token);
    }
}
