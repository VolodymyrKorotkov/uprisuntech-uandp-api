<?php

namespace App\Repository;

use App\Entity\OauthState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OauthState>
 *
 * @method OauthState|null find($id, $lockMode = null, $lockVersion = null)
 * @method OauthState|null findOneBy(array $criteria, array $orderBy = null)
 * @method OauthState[]    findAll()
 * @method OauthState[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OauthStateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OauthState::class);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getByUserState(string $state): OauthState
    {
        return $this
            ->createQueryBuilder('s')
            ->andWhere('s.userState = :state')
            ->setParameter('state', $state)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getByState(string $state): OauthState
    {
        return $this
            ->createQueryBuilder('s')
            ->andWhere('s.state = :state')
            ->setParameter('state', $state)
            ->getQuery()
            ->getSingleResult();
    }
}
