<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\OrganizationJoinTask;
use App\Enum\OrganizationJoinStatusEnum;
use App\Security\DataOnlyForOwnerRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

final class OrganizationJoinTaskRepository extends ServiceEntityRepository implements DataOnlyForOwnerRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrganizationJoinTask::class);
    }

    public function save(OrganizationJoinTask $task): void
    {
        $this->getEntityManager()->persist($task);
        $this->getEntityManager()->flush();
    }

    public function handleQueryForOwner(QueryBuilder $qb, string $userIdentity, array $roles): void
    {
        $rootAlias = $qb->getRootAliases()[0];

        $joinAlias = 'user_'.uniqid();
        $qb->leftJoin($rootAlias.'.user', $joinAlias);

        $qb
            ->andWhere($joinAlias.'.userIdentifier = :userIdentifier')
            ->setParameter('userIdentifier', $userIdentity);
    }

    /**
     * @param int $flowId
     * @param OrganizationJoinStatusEnum $status
     * @return array<OrganizationJoinTask>
     */
    public function getListByStatus(int $flowId, OrganizationJoinStatusEnum $status): array
    {
        return $this->findBy([
            'flow' => $flowId,
            'status' => $status
        ]);
    }

    /**
     * @param array<OrganizationJoinTask> $tasks
     * @return void
     */
    public function saveMultiple(array $tasks): void
    {
        foreach ($tasks as $task){
            $this->getEntityManager()->persist($task);
            $this->getEntityManager()->flush();
        }
    }
}
