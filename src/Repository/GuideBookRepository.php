<?php

namespace App\Repository;

use App\Entity\GuideBook;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GuideBook>
 *
 * @method GuideBook|null find($id, $lockMode = null, $lockVersion = null)
 * @method GuideBook|null findOneBy(array $criteria, array $orderBy = null)
 * @method GuideBook[]    findAll()
 * @method GuideBook[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuideBookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GuideBook::class);
    }


    public function setEnableStatus(int $id, bool $enable): void
    {
        $this->_em->createQueryBuilder()
            ->update(GuideBook::class, 'g')
            ->set('g.enable', ':enable')
            ->where('g.id = :id')
            ->setParameter('status', $enable)
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
    }


    public function softDelete(int $id): void
    {
        $this->_em->createQueryBuilder()
            ->update(GuideBook::class, 'g')
            ->set('g.deletedAt', ':deletedAt')
            ->where('g.id = :id')
            ->setParameter('deletedAt', new \DateTimeImmutable())
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
    }

//    /**
//     * @return GuideBook[] Returns an array of GuideBook objects
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

//    public function findOneBySomeField($value): ?GuideBook
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
