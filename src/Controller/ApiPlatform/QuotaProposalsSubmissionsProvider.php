<?php declare(strict_types=1);

namespace App\Controller\ApiPlatform;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Service\ManagerQuotaProvider;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Throwable;

final readonly class QuotaProposalsSubmissionsProvider implements ProviderInterface
{
    public function __construct(
        private ManagerQuotaProvider $managerQuotaProvider
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $appNumber = $context['filters']['applicationNumber'] ?? null;
        if (empty($appNumber)) {
            throw new BadRequestException('App number is empty');
        }

        return $this->managerQuotaProvider->findConfirmedQuotaSubmissions([$appNumber]);
    }
}
