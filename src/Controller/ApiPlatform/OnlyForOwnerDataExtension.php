<?php declare(strict_types=1);

namespace App\Controller\ApiPlatform;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Enum\UserRoleEnum;
use App\Security\DataOnlyForOwnerRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class OnlyForOwnerDataExtension implements QueryCollectionExtensionInterface
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        if ($this->security->isGranted(UserRoleEnum::ROLE_SUPER_ADMIN_CASE->value)){
            return;
        }

        $repository = $this->em->getRepository($resourceClass);
        if (!$repository instanceof DataOnlyForOwnerRepositoryInterface){
            return;
        }

        $repository->handleQueryForOwner(
            $queryBuilder,
            $this->security->getUser()->getUserIdentifier(),
            $this->security->getUser()->getRoles()
        );
    }
}
