<?php declare(strict_types=1);

namespace App\Controller\ApiPlatform\Me;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Security\ApplicationUserSecurity;

final readonly class CurrentUserProvider implements ProviderInterface
{
    public function __construct(
        private ApplicationUserSecurity $applicationUserSecurity
    ){}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->applicationUserSecurity->getUser();
    }
}
