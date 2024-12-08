<?php

namespace App\Repository;

use App\Entity\FormIoProcessResource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FormIoProcessResource>
 *
 * @method FormIoProcessResource|null find($id, $lockMode = null, $lockVersion = null)
 * @method FormIoProcessResource|null findOneBy(array $criteria, array $orderBy = null)
 * @method FormIoProcessResource[]    findAll()
 * @method FormIoProcessResource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormIoProcessResourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormIoProcessResource::class);
    }

//    /**
//     * @return FormIoProcessResource[] Returns an array of FormIoProcessResource objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FormIoProcessResource
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
