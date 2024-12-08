<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\InstallerEmail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class InstallerEmailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InstallerEmail::class);
    }

    /**
     * @return array<InstallerEmail>
     */
    public function findAllEnabled(string|null $state, int|null $zipcode): array
    {
        $qb = $this->createQueryBuilder('i');
        if ($state) {
            $qb->andWhere('i.stateShort = :stateShort')->setParameter('stateShort', $state);
        }

        if ($zipcode) {
            $qb
                ->andWhere('i.zipCodeMax >= :zipCode')
                ->andWhere('i.zipCodeMin <= :zipCode')
                ->setParameter('zipCode', $zipcode);
        }

        return $qb->getQuery()->getResult();
    }
}
