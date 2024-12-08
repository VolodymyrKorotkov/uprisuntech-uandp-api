<?php
namespace App\Repository;

use App\Entity\Certificate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

class CertificateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Certificate::class);
    }

    public function findUserCertificateById(null|UserInterface $user, int $id): ?Certificate
    {
        return $this->findOneBy(['userName' => $user->getUserIdentifier(), 'id' => $id]);
    }

    public function save(Certificate $certificate): void
    {
        if (!$this->_em->contains($certificate)) {
            $this->_em->persist($certificate);
        }
        $this->_em->flush();
    }

}
