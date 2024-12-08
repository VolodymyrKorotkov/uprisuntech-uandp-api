<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\UserService\Entity\Networks;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class NetworksRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Networks::class);
        $this->entityManager = $registry->getManager();
    }

    public function attach(User $user, Networks $network): void
    {
        $network->setUser($user);
        $this->entityManager->persist($network);
        $this->entityManager->flush();
    }

    public function findByUserId(int $userId): array
    {
        return $this->findBy(['userId' => $userId]);
    }

    public function findByUserIdAndProvider(int $userId, string $provider): ?Networks
    {
        return $this->findOneBy([
            'userId' => $userId,
            'provider' => $provider
        ]);
    }
}

