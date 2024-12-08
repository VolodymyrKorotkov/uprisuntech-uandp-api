<?php

namespace App\Repository;

use App\Entity\GuideBookTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GuideBookTranslation>
 *
 * @method GuideBookTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method GuideBookTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method GuideBookTranslation[]    findAll()
 * @method GuideBookTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuideBookTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GuideBookTranslation::class);
    }

//    /**
//     * @return GuideBookTranslation[] Returns an array of GuideBookTranslation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GuideBookTranslation
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
