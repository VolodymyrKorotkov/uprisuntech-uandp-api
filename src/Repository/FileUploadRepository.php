<?php

namespace App\Repository;

use App\Entity\FileUpload;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<FileUpload>
 *
 * @method FileUpload|null find($id, $lockMode = null, $lockVersion = null)
 * @method FileUpload|null findOneBy(array $criteria, array $orderBy = null)
 * @method FileUpload[]    findAll()
 * @method FileUpload[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileUploadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FileUpload::class);
    }

    public function findPathByIdentifier(string $identifier): ?string
    {
        $path = $this->createQueryBuilder('f')
            ->select('f.path')
            ->andWhere('f.identifier = :identifier')
            ->setParameter('identifier', $identifier)
            ->getQuery()
            ->getOneOrNullResult();
        return $path ? $path['path'] : null;
    }

    public function save(FileUpload $fileUpload): void
    {
        $this->_em->persist($fileUpload);
        $this->_em->flush();
    }

}
