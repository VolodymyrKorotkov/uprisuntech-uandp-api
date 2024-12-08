<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\RefreshToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RefreshTokenRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, RefreshToken::class);
	}

	public function findByToken(string $code): ?RefreshToken
	{
		return $this->findOneBy(['refreshToken' => $code]);
	}

	public function findByUsername(string $username): ?RefreshToken
	{
		return $this->findOneBy(['username' => $username]);
	}

    public function save(RefreshToken $refreshToken): void
    {
        if (!$this->_em->contains($refreshToken)) {
            $this->_em->persist($refreshToken);
        }
        $this->_em->flush();
    }
}
