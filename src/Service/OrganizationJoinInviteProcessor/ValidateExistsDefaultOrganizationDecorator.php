<?php declare(strict_types=1);

namespace App\Service\OrganizationJoinInviteProcessor;

use App\Entity\OrganizationUserRole;
use App\Repository\OrganizationRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsDecorator(OrganizationJoinInviteProcessorInterface::class, priority: self::VALIDATE_EXISTS_ORGANIZATION)]
final readonly class ValidateExistsDefaultOrganizationDecorator implements OrganizationJoinInviteProcessorInterface
{
    public function __construct(
        private OrganizationJoinInviteProcessorInterface $processor,
        private OrganizationRepository $organizationRepository
    )
    {
    }

    /**
     * @throws NonUniqueResultException
     */
    public function processOrganizationJoinInvite(ProcessOrganizationJoinInviteDto $dto): OrganizationUserRole
    {
        if ($this->organizationRepository->hasUserDefaultOrganization($dto->userIdentity)){
            throw new BadRequestHttpException('You are already a member of the organization');
        }

        return $this->processor->processOrganizationJoinInvite($dto);
    }
}
