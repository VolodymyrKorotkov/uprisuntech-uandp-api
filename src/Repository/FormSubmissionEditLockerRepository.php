<?php

namespace App\Repository;

use App\Entity\FormIo;
use App\Entity\FormSubmissionEditLocker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FormSubmissionEditLocker>
 *
 * @method FormSubmissionEditLocker|null find($id, $lockMode = null, $lockVersion = null)
 * @method FormSubmissionEditLocker|null findOneBy(array $criteria, array $orderBy = null)
 * @method FormSubmissionEditLocker[]    findAll()
 * @method FormSubmissionEditLocker[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormSubmissionEditLockerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormSubmissionEditLocker::class);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getBySubmissionId(string $submissionId): FormSubmissionEditLocker
    {
        return $this->findOneBy([
            'submissionId' => $submissionId
        ]) ?? throw EntityNotFoundException::noIdentifierFound(FormIo::class);
    }

    public function save(FormSubmissionEditLocker $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }
}
