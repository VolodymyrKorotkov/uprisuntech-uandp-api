<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\OrganizationJoinInvite;
use App\Enum\OrganizationJoinInviteStatusEnum;
use App\Security\DataOnlyForOwnerRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

final class OrganizationJoinInviteRepository extends ServiceEntityRepository implements DataOnlyForOwnerRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrganizationJoinInvite::class);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getByUserState(string $state): OrganizationJoinInvite
    {
        return
            $this->findOneBy(['oauthUserState' => $state]) ??
            throw EntityNotFoundException::noIdentifierFound(OrganizationJoinInvite::class);
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

    public function save(OrganizationJoinInvite $invite): void
    {
        $this->getEntityManager()->persist($invite);
        $this->getEntityManager()->flush();
    }

    public function isInvitedStatus(int $id): bool
    {
        return $this->count([
            'id' => $id,
            'status' => OrganizationJoinInviteStatusEnum::INVITED
        ]) > 0;
    }
}
