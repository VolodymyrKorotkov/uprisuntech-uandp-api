<?php declare(strict_types=1);

namespace App\Repository\FormTaskSubmission;

use App\Entity\FormTaskSubmission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

final class FormTaskSubmissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormTaskSubmission::class);
    }

    /**
     * @throws EntityNotFoundException
     * @throws NonUniqueResultException
     */
    public function getForTask(string $taskId, string $formKey): FormTaskSubmission
    {
        $qb = $this->createQueryBuilder('ts');
        $qb
            ->leftJoin('ts.form', 'tsf')
            ->andWhere('tsf.formKey = :formKey')
            ->andWhere('ts.taskId = :taskId')
            ->setParameter('formKey', $formKey)
            ->setParameter('taskId', $taskId);

        return $qb->getQuery()->getOneOrNullResult() ?? throw EntityNotFoundException::noIdentifierFound(FormTaskSubmission::class);
    }

    /**
     * @param string $processId
     * @param string $formKey
     * @return array<FormTaskSubmission>
     */
    public function findForProcess(string $processId, string $formKey): array
    {
        $qb = $this->createQueryBuilder('ts');
        $qb
            ->leftJoin('ts.form', 'tsf')
            ->andWhere('tsf.formKey = :formKey')
            ->andWhere('ts.processId = :processId')
            ->setParameter('formKey', $formKey)
            ->setParameter('processId', $processId);

        return $qb->getQuery()->getResult();
    }

    public function save(FormTaskSubmission $taskSubmission): void
    {
        $this->getEntityManager()->persist($taskSubmission);
        $this->getEntityManager()->flush();
    }

    public function findForSubmission(string $submissionId): FormTaskSubmissionCollection
    {
        return new FormTaskSubmissionCollection(
            $this->findBy(['submissionId' => $submissionId])
        );
    }
}
