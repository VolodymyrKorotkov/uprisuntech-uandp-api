<?php declare(strict_types=1);

namespace App\Controller\Admin;

use App\Enum\AppRoutePrefixEnum;
use App\Service\KeycloakUserSync\Dto\SyncToKeycloakDto;
use App\Service\KeycloakUserSync\KeycloakUserSynchronizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
final class SyncToKeycloakController extends AbstractController
{
    public function __construct(
        private readonly KeycloakUserSynchronizerInterface $synchronizer
    ){}

    #[Route(path: AppRoutePrefixEnum::API_ADMIN->value . "/users/sync-to-keycloak",
        name: 'admin.sync_to_keycloak',
        methods: 'POST'
    )]
    public function sync(
        #[MapRequestPayload] SyncToKeycloakDto $dto
    ): JsonResponse
    {
        $this->synchronizer->syncUserToKeycloak($dto);

        return new JsonResponse("User synchronization in Keycloak was successful.");
    }
}
