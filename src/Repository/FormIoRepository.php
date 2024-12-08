<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\FormIo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;

final class FormIoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormIo::class);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getByFormId(string $formId): FormIo
    {
        return $this->findOneBy([
            'formId' => $formId
        ]) ?? throw EntityNotFoundException::noIdentifierFound(FormIo::class);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getByKey(string $key): FormIo
    {
        return $this->findOneBy([
            'formKey' => $key
        ]) ?? throw EntityNotFoundException::noIdentifierFound(FormIo::class);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getApplicationPublicForm(): FormIo
    {
        return $this->findOneBy(['applicationPublicForm' => true]) ?? throw EntityNotFoundException::noIdentifierFound(FormIo::class);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getInstallerProposalForm(): FormIo
    {
        return $this->findOneBy(['installerProposalForm' => true]) ?? throw EntityNotFoundException::noIdentifierFound(FormIo::class);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getManagerProposalForm(): FormIo
    {
        return $this->findOneBy(['managerProposalForm' => true]) ?? throw EntityNotFoundException::noIdentifierFound(FormIo::class);
    }

    public function save(FormIo $form): void
    {
        $this->getEntityManager()->persist($form);
        $this->getEntityManager()->flush();
    }
}
