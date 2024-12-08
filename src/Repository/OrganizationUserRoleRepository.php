<?php

namespace App\Repository;

use App\Entity\OrganizationUserRole;
use App\Entity\User;
use App\Security\DataOnlyForOwnerRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrganizationUserRole>
 *
 * @method OrganizationUserRole|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrganizationUserRole|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrganizationUserRole[]    findAll()
 * @method OrganizationUserRole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrganizationUserRoleRepository extends ServiceEntityRepository implements DataOnlyForOwnerRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrganizationUserRole::class);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getUserIdentity(int $organizationId, string $role): OrganizationUserRole
    {
        $role = $this->findOneBy(['organization' => $organizationId, 'role' => $role]);
        if (!$role) {
            throw EntityNotFoundException::noIdentifierFound(OrganizationUserRole::class);
        }

        return $role;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getUserRole(int $organizationId, string $userIdentity): OrganizationUserRole
    {
        // todo: Fix userIdentifier on OrganizationUserRole
        $role = $this->findOneBy(['organization' => $organizationId, 'userIdentifier' => $userIdentity]);
        if (!$role) {
            throw EntityNotFoundException::noIdentifierFound(OrganizationUserRole::class);
        }

        return $role;
    }

    /**
     * @param int $organizationId
     * @param int $userId
     * @return array<OrganizationUserRole>
     */
    public function getUserRoleList(int $organizationId, int $userId): array
    {
        return $this->findBy(['organization' => $organizationId, 'user' => $userId]);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function hasUserRole(int $organizationId, string $userIdentity): bool
    {
        return $this->createQueryBuilder('r')
                ->select('count(r.id)')
                ->leftJoin('r.user', 'ru')
                ->andWhere('ru.userIdentifier = :userIdentifier')
                ->andWhere('r.organization = :organization')
                ->setParameter('userIdentifier', $userIdentity)
                ->setParameter('organizationId', $organizationId)
                ->getQuery()
                ->getSingleScalarResult() > 0;
    }

    /**
     * @throws EntityNotFoundException
     * @throws NonUniqueResultException
     */
    public function getOneDefaultRole(string $userIdentifier): OrganizationUserRole
    {
        return $this
            ->createQueryBuilder('r')
            ->leftJoin('r.user', 'ru')
            ->andWhere('ru.'.User::USERNAME_FIELD.' = :userIdentifier')
            ->andWhere('r.default = true')
            ->setParameter('userIdentifier', $userIdentifier)

            ->getQuery()
            ->getOneOrNullResult() ??
            throw EntityNotFoundException::noIdentifierFound(OrganizationUserRole::class);
    }

    /**
     * @param string $userIdentifier
     * @return OrganizationUserRole[]
     */
    public function getAllDefaultRoles(string $userIdentifier): array
    {
        return $this
            ->createQueryBuilder('r')
            ->leftJoin('r.user', 'ru')
            ->andWhere('ru.userIdentifier = :userIdentifier')
            ->andWhere('r.default = true')
            ->setParameter('userIdentifier', $userIdentifier)

            ->getQuery()
            ->getResult();
    }

    public function handleQueryForOwner(QueryBuilder $qb, string $userIdentity, array $roles): void
    {
        $rootAlias = $qb->getRootAliases()[0];

        $orgAlias = 'org_'.uniqid();
        $userAlias = 'user_'.uniqid();
        $qb
            ->leftJoin($rootAlias.'.organization', $orgAlias)
            ->leftJoin($orgAlias.'.user', $userAlias);

        $qb
            ->andWhere($userAlias.'.userIdentifier = :userIdentifier')
            ->setParameter('userIdentifier', $userIdentity);
    }

    public function saveMultiple(array $roles): void
    {
        foreach ($roles as $role) {
            $this->getEntityManager()->persist($role);
            $this->getEntityManager()->flush();
        }
    }

    public function save(OrganizationUserRole $role): void
    {
        $this->getEntityManager()->persist($role);
        $this->getEntityManager()->flush();
    }

    /**
     * @param array<OrganizationUserRole> $roles
     * @return void
     */
    public function removeMultiple(array $roles): void
    {
        foreach ($roles as $role) {
            $this->getEntityManager()->remove($role);
        }
        $this->getEntityManager()->flush();
    }
}
