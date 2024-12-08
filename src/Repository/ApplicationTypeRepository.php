<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\ApplicationType;
use App\Enum\ApplicationStrategyEnum;
use App\Security\DataOnlyForOwnerRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

final class ApplicationTypeRepository extends ServiceEntityRepository implements DataOnlyForOwnerRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApplicationType::class);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getById(int $id): ApplicationType
    {
        return
            $this->findOneBy(['id' => $id]) ??
            throw EntityNotFoundException::noIdentifierFound(ApplicationType::class);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getByAlias(string $alias): ApplicationType
    {
        return
            $this->findOneBy(['alias' => $alias]) ??
            throw EntityNotFoundException::noIdentifierFound(ApplicationType::class);
    }

    /**
     * @param ApplicationStrategyEnum $strategyEnum
     * @return array<ApplicationType>
     */
    public function findByStrategy(ApplicationStrategyEnum $strategyEnum): array
    {
        return $this->findBy([
            'strategyType' => $strategyEnum
        ]);
    }

    public function handleQueryForOwner(QueryBuilder $qb, string $userIdentity, array $roles): void
    {
        $rootAlias = $qb->getRootAliases()[0];

        $expr = $qb->expr();
        $qb
            ->andWhere(
                $expr->in($rootAlias . '.role', ':roles')
            )
            ->andWhere(
                $expr->in($rootAlias . '.enabled', ':enabled')
            )
            ->setParameter('roles', $roles)
            ->setParameter('enabled', true);
    }
}
