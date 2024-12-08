<?php declare(strict_types=1);

namespace App\Repository\FormProcessSubmission;

use App\Entity\FormProcessSubmission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

final class FormProcessSubmissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormProcessSubmission::class);
    }

    /**
     * @throws EntityNotFoundException
     * @throws NonUniqueResultException
     */
    public function getForProcessForm(string $processInstance, string $formKey): FormProcessSubmission
    {
        $qb = $this->createQueryBuilder('cps');
        $qb
            ->leftJoin('cps.form', 'cpsf')
            ->andWhere('cpsf.formKey = :formKey')
            ->andWhere('cps.processInstanceId = :processInstanceId')
            ->setParameter('formKey', $formKey)
            ->setParameter('processInstanceId', $processInstance);

        return $qb->getQuery()->getOneOrNullResult() ?? throw EntityNotFoundException::noIdentifierFound(FormProcessSubmission::class);
    }

    /**
     * @throws EntityNotFoundException
     * @throws NonUniqueResultException
     */
    public function getForProcessByFormId(string $processInstance, string $formId): FormProcessSubmission
    {
        $qb = $this->createQueryBuilder('cps');
        $qb
            ->leftJoin('cps.form', 'cpsf')
            ->andWhere('cpsf.formId = :formId')
            ->andWhere('cps.processInstanceId = :processInstanceId')
            ->setParameter('formId', $formId)
            ->setParameter('processInstanceId', $processInstance);

        return $qb->getQuery()->getOneOrNullResult() ?? throw EntityNotFoundException::noIdentifierFound(FormProcessSubmission::class);
    }

    public function findForProcess(string $processInstance): FormProcessSubmissionCollection
    {
        return new FormProcessSubmissionCollection(
            $this->findBy(['processInstanceId' => $processInstance])
        );
    }

    public function findForSubmission(string $submissionId): FormProcessSubmissionCollection
    {
        return new FormProcessSubmissionCollection(
            $this->findBy(['submissionId' => $submissionId])
        );
    }

    public function findByIds(array $submissionIds): array
    {
        return $this->findBy([
            'submissionId' => $submissionIds
        ]);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getBySubmissionId(string $submissionId): FormProcessSubmission
    {
        return $this->findOneBy(['submissionId' => $submissionId]) ?? throw EntityNotFoundException::noIdentifierFound(FormProcessSubmission::class);
    }

    public function save(FormProcessSubmission $submission): void
    {
        $this->getEntityManager()->persist($submission);
        $this->getEntityManager()->flush();
    }
}
