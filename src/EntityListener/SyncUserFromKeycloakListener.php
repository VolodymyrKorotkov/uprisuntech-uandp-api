<?php declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\SyncUserFromKeycloak;
use App\Service\KeycloakUserSync\KeycloakUserSyncInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postPersist, method: 'handle', entity: SyncUserFromKeycloak::class)]
final readonly class SyncUserFromKeycloakListener
{
    public function __construct(
        private KeycloakUserSyncInterface $keycloakUserSync
    )
    {}

    public function handle(SyncUserFromKeycloak $fromKeycloak): void
    {
        if (!$fromKeycloak->hasUser()) {
            $this->keycloakUserSync->syncUserFromKeycloakByEmail($fromKeycloak);
        }
    }
}
