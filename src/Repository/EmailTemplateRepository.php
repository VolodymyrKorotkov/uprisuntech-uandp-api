<?php

namespace App\Repository;

use App\Entity\EmailTemplate;
use App\Enum\EmailTemplateUseInEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmailTemplate>
 *
 * @method EmailTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailTemplate[]    findAll()
 * @method EmailTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailTemplate::class);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getByUsIn(EmailTemplateUseInEnum $useIn): EmailTemplate
    {
        return $this->findOneBy(['useIn' => $useIn]) ?? throw EntityNotFoundException::noIdentifierFound(EmailTemplate::class);
    }
}
