<?php

declare(strict_types=1);

namespace App\Repository;

use App\UserService\Entity\UserIdGovData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class GovUaDataRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, UserIdGovData::class);
	}

    public function save(UserIdGovData $idGovData): void
    {
        if (!$this->_em->contains($idGovData)) {
            $this->_em->persist($idGovData);
        }
        $this->_em->flush();
    }
}
