<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\ApplicationProcess;
use App\Entity\User;
use App\Security\DataOnlyForOwnerRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

final class ApplicationProcessRepository extends ServiceEntityRepository implements DataOnlyForOwnerRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApplicationProcess::class);
    }

    public function save(ApplicationProcess $process): void
    {
        $this->getEntityManager()->persist($process);
        $this->getEntityManager()->flush();
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getByProcessID(string $processID): ApplicationProcess
    {
        return
            $this->findOneBy(['processInstanceId' => $processID]) ??
            throw EntityNotFoundException::noIdentifierFound(ApplicationProcess::class);
    }

    public function handleQueryForOwner(QueryBuilder $qb, string $userIdentity, array $roles): void
    {
        $rootAlias = $qb->getRootAliases()[0];

        $joinAlias = 'user_' . uniqid();
        $qb->leftJoin($rootAlias . '.user', $joinAlias);

        $qb
            ->andWhere($joinAlias . '.' . User::USERNAME_FIELD . ' = :userIdentifier')
            ->setParameter('userIdentifier', $userIdentity);
    }
}
