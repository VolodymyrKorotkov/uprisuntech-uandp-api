<?php

namespace App\Repository\ApplicationTask;

use App\Entity\ApplicationTask;
use App\Security\DataOnlyForOwnerRepositoryInterface;
use App\Service\AggregateTaskProvider\Dto\TasksSourceFilterDto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<ApplicationTask>
 *
 * @method ApplicationTask|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApplicationTask|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApplicationTask[]    findAll()
 * @method ApplicationTask[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NativeTaskRepository extends ServiceEntityRepository implements DataOnlyForOwnerRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApplicationTask::class);
    }

    public function save(ApplicationTask $submission): void
    {
        $this->getEntityManager()->persist($submission);
        $this->getEntityManager()->flush();
    }

    public function handleQueryForOwner(QueryBuilder $qb, string $userIdentity, array $roles): void
    {
        $rootAlias = $qb->getRootAliases()[0];

//        $joinAlias = 'user_' . uniqid();
//        $qb->leftJoin($rootAlias . '.user', $joinAlias);

        $expr = $qb->expr();
        $qb
            ->andWhere(
                $expr->orX(
                    $expr->eq($rootAlias . '.userIdentifier', ':userIdentifier'),
                    $expr->in($rootAlias . '.role', ':roles'),
                )
            )
            ->setParameter('userIdentifier', $userIdentity)
            ->setParameter('roles', $roles);
    }

    /**
     * @return Paginator<ApplicationTask>
     */
    public function getSourceTasks(TasksSourceFilterDto $filterDto): Paginator
    {
        $qb = $this->createQueryBuilder('t');
        $expr = $qb->expr();

        $qb
           // ->leftJoin('t.user', 'tu')
            ->andWhere(
                $expr->orX(
                    $expr->eq('t.userIdentifier', ':userIdentifier'),
                    $expr->in('t.role', ':roles'),
                )
            )
            ->setParameter('userIdentifier', $filterDto->getUser()->getUserIdentifier())
            ->setParameter('roles', $filterDto->getUser()->getRoles())

            ->andWhere($expr->eq('t.type', ':type'))
            ->setParameter('type', $filterDto->getApplicationType())

            ->addOrderBy('t.completed', 'DESC')
            ->addOrderBy('t.updatedAt', 'DESC')

            ->setFirstResult($filterDto->getOffset())
            ->setMaxResults($filterDto->getItemsPerPage());

        return new Paginator($qb);
    }

    public function getByTaskId(string $taskId): ApplicationTask
    {
        return
            $this->findOneBy(['taskId' => $taskId]) ??
            throw new NotFoundHttpException('Task not found');
    }
}
