<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\SyncUserFromKeycloak;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class SyncUserFromKeycloakRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SyncUserFromKeycloak::class);
    }

    public function save(SyncUserFromKeycloak $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }
}
