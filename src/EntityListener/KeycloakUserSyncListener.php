<?php

namespace App\EntityListener;

use App\Entity\User;
use App\Service\KeycloakUserSync\KeycloakUserSynchronizerInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, method: 'handle', entity: User::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'handle', entity: User::class)]
final readonly class KeycloakUserSyncListener
{
    public function __construct(
        private KeycloakUserSynchronizerInterface $synchronizer
    ){}

    public function handle(User $user): void
    {
        $syncedUserResult = $this->synchronizer->createOrUpdateUserOnKeycloak($user);

        $user->setDrfoCode($syncedUserResult->getDrfoCode());
        $user->setEmail($syncedUserResult->getEmail());
    }
}
