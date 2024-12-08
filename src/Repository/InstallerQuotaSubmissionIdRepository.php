<?php

namespace App\Repository;

use App\Entity\InstallerQuotaSubmissionId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InstallerQuotaSubmissionId>
 *
 * @method InstallerQuotaSubmissionId|null find($id, $lockMode = null, $lockVersion = null)
 * @method InstallerQuotaSubmissionId|null findOneBy(array $criteria, array $orderBy = null)
 * @method InstallerQuotaSubmissionId[]    findAll()
 * @method InstallerQuotaSubmissionId[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InstallerQuotaSubmissionIdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InstallerQuotaSubmissionId::class);
    }

//    /**
//     * @return InstallerQuotaSubmissionId[] Returns an array of InstallerQuotaSubmissionId objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?InstallerQuotaSubmissionId
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
