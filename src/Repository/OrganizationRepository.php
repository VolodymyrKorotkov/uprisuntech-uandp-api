<?php

namespace App\Repository;

use App\Entity\Organization;
use App\Enum\UserRoleEnum;
use App\Security\DataOnlyForOwnerRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Organization>
 *
 * @method Organization|null find($id, $lockMode = null, $lockVersion = null)
 * @method Organization|null findOneBy(array $criteria, array $orderBy = null)
 * @method Organization[]    findAll()
 * @method Organization[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrganizationRepository extends ServiceEntityRepository implements DataOnlyForOwnerRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Organization::class);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getUserDefaultOrganization(string $userIdentifier): Organization
    {
        return $this->getUserDefaultOrganizationQueryBuilder($userIdentifier)
            ->getQuery()
            ->getSingleResult();
    }

    public function hasDefaultOrganizationAsMunicipalityHead(string $userIdentifier): bool
    {
        try {
            $countOrganization = $this->getDefaultOrganizationAsMunicipalityHeadQueryBuilder($userIdentifier)
                ->select('count(o.id)')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException){
            return false;
        }

        return $countOrganization > 0;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function hasUserDefaultOrganization(string $userIdentifier): bool
    {
        return null !== $this->getUserDefaultOrganizationQueryBuilder($userIdentifier)->getQuery()->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getDefaultOrganization(): Organization
    {
        return $this->createQueryBuilder('o')->andWhere('o.default = true')->getQuery()->getSingleResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getOtherDefaultOrganization(?int $currentDefaultId)
    {
        return $this
            ->createQueryBuilder('o')
            ->andWhere('o.default = true')
            ->andWhere('o.id != :currentDefaultId')
            ->setParameter('currentDefaultId', $currentDefaultId)
            ->getQuery()
            ->getSingleResult();
    }

    public function save(Organization $organization): void
    {
        $this->getEntityManager()->persist($organization);
        $this->getEntityManager()->flush();
    }

    public function handleQueryForOwner(QueryBuilder $qb, string $userIdentity, array $roles): void
    {
        $rootAlias = $qb->getRootAliases()[0];

        $qb
            ->innerJoin($rootAlias . '.roles', 'r')
            ->innerJoin('r.user', 'ru')
            ->andWhere('ru.userIdentifier = :userIdentifier')
            ->setParameter('userIdentifier', $userIdentity);
    }

    /**
     * @param string $userIdentifier
     * @return QueryBuilder
     */
    private function getUserDefaultOrganizationQueryBuilder(string $userIdentifier): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.roles', 'r')
            ->innerJoin('r.user', 'ru')
            ->andWhere('ru.userIdentifier = :userIdentifier')
            ->andWhere('r.default = true')
            ->setParameter('userIdentifier', $userIdentifier);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getDefaultOrganizationAsMunicipalityHead(string $userIdentifier): Organization
    {
        return $this
            ->getDefaultOrganizationAsMunicipalityHeadQueryBuilder($userIdentifier)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function isExistsEdrpou(string $edrpou): bool
    {
        return $this->createQueryBuilder('o')
                ->select('count(o.id)')
                ->andWhere('o.edrpou = :edrpou')
                ->setParameter('edrpou', $edrpou)
                ->getQuery()
                ->getSingleScalarResult() > 0;
    }

    public function hasByTitle(string $title): bool
    {
        return $this->count(['title' => $title]) > 0;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getByEdrpou(string $edrpou): Organization
    {
        return
            $this->findOneBy(['edrpou' => $edrpou]) ??
            throw EntityNotFoundException::noIdentifierFound(Organization::class);
    }

    /**
     * @param string $userIdentifier
     * @return QueryBuilder
     */
    public function getDefaultOrganizationAsMunicipalityHeadQueryBuilder(string $userIdentifier): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.roles', 'roles')
            ->leftJoin('roles.user', 'rolesUser')
            ->andWhere('rolesUser.userIdentifier = :userIdentifier')
            ->andWhere('roles.role = :role')
            ->andWhere('roles.default = true')
            ->setParameter('userIdentifier', $userIdentifier)
            ->setParameter('role', UserRoleEnum::ROLE_MUNICIPALITY_HEAD);
    }
}
