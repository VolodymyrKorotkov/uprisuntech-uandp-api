<?php declare(strict_types=1);

namespace App\Repository;

use App\ApplicationFlow\Entity\Application;
use App\ApplicationFlow\Entity\ApplicationForm;
use App\Entity\FormIo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;

final class ApplicationFormRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApplicationForm::class);
    }

    public function save(ApplicationForm $formSubmission): void
    {
        $this->getEntityManager()->persist($formSubmission);
        $this->getEntityManager()->flush();
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getApplicationSubmission(int|Application $application, int|FormIo $form): ApplicationForm
    {
        $applicationSubmission = $this->findOneBy([
            'application' => $application,
            'form' => $form
        ]);
        if (!$applicationSubmission){
            throw EntityNotFoundException::noIdentifierFound(ApplicationForm::class);
        }

        return $applicationSubmission;
    }

    public function applicationSubmissionExits(int|Application $application, int|FormIo $form): bool
    {
        return $this->count([
                'application' => $application,
                'form' => $form
            ]) > 0;
    }
}
