<?php declare(strict_types=1);

namespace App\Repository\FormProcessSubmissionVariable;

use App\Entity\FormProcessSubmissionVariable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class FormProcessSubmissionVariableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormProcessSubmissionVariable::class);
    }

    public function save(FormProcessSubmissionVariable $submission): void
    {
        $this->getEntityManager()->persist($submission);
        $this->getEntityManager()->flush();
    }

    public function findForProcess(string $processId, array $varsKeys = []): FormProcessSubmissionVariableCollection
    {
        $filter = ['processInstanceId' => $processId];
        if ($varsKeys){
            $filter['key'] = $varsKeys;
        }

        return new FormProcessSubmissionVariableCollection(
            $this->findBy($filter)
        );
    }

    public function persist(FormProcessSubmissionVariable $submission): void
    {
        $this->getEntityManager()->persist($submission);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
