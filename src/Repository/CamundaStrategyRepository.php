<?php

namespace App\Repository;

use App\Entity\CamundaStrategy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CamundaStrategy>
 *
 * @method CamundaStrategy|null find($id, $lockMode = null, $lockVersion = null)
 * @method CamundaStrategy|null findOneBy(array $criteria, array $orderBy = null)
 * @method CamundaStrategy[]    findAll()
 * @method CamundaStrategy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CamundaStrategyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CamundaStrategy::class);
    }
}
