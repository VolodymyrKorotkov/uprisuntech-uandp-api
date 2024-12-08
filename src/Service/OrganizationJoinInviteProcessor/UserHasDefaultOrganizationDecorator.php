<?php

namespace App\Service\OrganizationJoinInviteProcessor;

use App\Entity\OrganizationUserRole;
use App\Enum\OrganizationJoinInviteStatusEnum;
use App\Repository\OrganizationJoinInviteRepository;
use App\Repository\OrganizationRepository;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsDecorator(OrganizationJoinInviteProcessorInterface::class)]
readonly class UserHasDefaultOrganizationDecorator implements OrganizationJoinInviteProcessorInterface
{
    public function __construct(
        private OrganizationJoinInviteProcessorInterface $inviteProcessor,
        private OrganizationRepository                   $organizationRepository,
        private OrganizationJoinInviteRepository         $organizationJoinInviteRepository,
    )
    {
    }

    /*
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function processOrganizationJoinInvite(ProcessOrganizationJoinInviteDto $dto): OrganizationUserRole
    {
        if (!$this->organizationRepository->hasDefaultOrganizationAsMunicipalityHead($dto->userIdentity)) {
            return $this->inviteProcessor->processOrganizationJoinInvite($dto);
        }

        $invite = $dto->invite;
        $invite->setStatus(OrganizationJoinInviteStatusEnum::ERROR_USER_HAS_ORGANIZATION);
        $this->organizationJoinInviteRepository->save($invite);

        throw new BadRequestHttpException('User already has default organization');
    }
}
