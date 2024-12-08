<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\OrganizationJoinFlow;
use App\Enum\OrganizationJoinStatusEnum;
use App\Security\DataOnlyForOwnerRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

final class OrganizationJoinFlowRepository extends ServiceEntityRepository implements DataOnlyForOwnerRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrganizationJoinFlow::class);
    }

    public function handleQueryForOwner(QueryBuilder $qb, string $userIdentity, array $roles): void
    {
        $rootAlias = $qb->getRootAliases()[0];

        $joinAlias = 'user_' . uniqid();
        $qb->leftJoin($rootAlias . '.user', $joinAlias);

        $qb
            ->andWhere($joinAlias . '.userIdentifier = :userIdentifier')
            ->setParameter('userIdentifier', $userIdentity);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function userHasOtherWithStatus(int $applicationUserId, int $excludeFlowId, OrganizationJoinStatusEnum $status): bool
    {
        return $this
            ->createQueryBuilder('f')

            ->select('count(f.id)')

            ->andWhere('f.user = :userId')
            ->andWhere('f.status = :status')
            ->andWhere('f.id != :flowId')

            ->setParameter('userId', $applicationUserId)
            ->setParameter('status', $status)
            ->setParameter('flowId', $excludeFlowId)

            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    public function save(?OrganizationJoinFlow $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }
}
