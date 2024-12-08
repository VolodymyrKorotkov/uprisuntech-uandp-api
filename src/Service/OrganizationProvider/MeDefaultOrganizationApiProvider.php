<?php

namespace App\Service\OrganizationProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Organization;
use App\Repository\OrganizationRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\SecurityBundle\Security;

class MeDefaultOrganizationApiProvider implements ProviderInterface
{
    private Security $security;
    private OrganizationRepository $organizationRepository;

    public function __construct(
        Security $security,
        OrganizationRepository $organizationRepository
    ){
        $this->security = $security;
        $this->organizationRepository = $organizationRepository;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Organization
    {
        try {
            return $this->organizationRepository->getUserDefaultOrganization($this->getUserIdentifier());
        } catch (NoResultException){
            return null;
        }
    }

    private function getUserIdentifier(): string
    {
        return $this->security->getUser()->getUserIdentifier();
    }
}
