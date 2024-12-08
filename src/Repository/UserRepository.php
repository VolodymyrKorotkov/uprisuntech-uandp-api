<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $user): void
    {
        if (!$this->_em->contains($user)) {
            $this->_em->persist($user);
        }
        $this->_em->flush();
    }

    public function findByUserIdentity(string $userName): ?User
    {
        return $this->findOneBy([User::USERNAME_FIELD => $userName]);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getByUserIdentity(string $userName): User
    {
        return $this->findOneBy([User::USERNAME_FIELD => $userName]) ?? throw EntityNotFoundException::noIdentifierFound(User::class);
    }

    public function existsByUserIdentity(string $userName): bool
    {
        return $this->count([User::USERNAME_FIELD => $userName]) > 0;
    }

    public function findByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getByEmail(string $email): User
    {
        return $this->findOneBy(['email' => $email]) ??
            throw EntityNotFoundException::noIdentifierFound(User::class);
    }

    public function findByDrfoCode(string $drfoCode): ?User
    {
        return $this->findOneBy(['drfoCode' => $drfoCode]);
    }

    public function findByEdrpouCode(string $edrpouCode): ?User
    {
        return $this->findOneBy(['edrpouCode' => $edrpouCode]);
    }

    public function findByCode(string $code): ?User
    {
        return $this->findOneBy(['code' => $code]);
    }

    public function findByToken(string $token): ?User
    {
        return $this->findOneBy(['token' => $token]);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getByToken(string $token): ?User
    {
        return
            $this->findOneBy(['token' => $token])
            ?? throw EntityNotFoundException::noIdentifierFound(User::class);
    }

    public function removeUser(User $user): void
    {
        $this->_em->remove($user);
        $this->_em->flush();
    }

    /**
     * @param array<User> $identities
     * @return array
     */
    public function findByIdentities(array $identities): array
    {
        return $this->findBy([
            User::USERNAME_FIELD => $identities
        ]);
    }
}
