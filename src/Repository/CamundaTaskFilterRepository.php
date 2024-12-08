<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\CamundaTaskFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class CamundaTaskFilterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CamundaTaskFilter::class);
    }
}