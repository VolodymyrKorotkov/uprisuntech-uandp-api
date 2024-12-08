<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SiteRedirect;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;

class SiteRedirectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SiteRedirect::class);
    }

    public function add(SiteRedirect $site): void
    {
        $this->_em->persist($site);
        $this->_em->flush();
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getByAlias(string $alias): SiteRedirect
    {
        return $this->findOneBy(['alias' => $alias]) ??
            throw EntityNotFoundException::noIdentifierFound(SiteRedirect::class);
    }
}
