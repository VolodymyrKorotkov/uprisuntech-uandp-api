<?php

namespace App\Service\OrganizationJoinStarter;

use App\Entity\OrganizationJoinFlow;
use App\Enum\OrganizationJoinStatusEnum;
use App\Repository\OrganizationJoinFlowRepository;
use App\Repository\OrganizationRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

#[AsDecorator(OrganizationJoinFlowStarterInterface::class)]
final readonly class OrganizationJoinLimitDecorator implements OrganizationJoinFlowStarterInterface
{
    public function __construct(
        private OrganizationJoinFlowStarterInterface $organizationJoinFlowStarter,
        private OrganizationJoinFlowRepository $joinRepository,
        private OrganizationRepository         $organizationRepository
    )
    {}

    private function hasJoinFlowsInProgress(OrganizationJoinFlow $flow): bool
    {
        return $this->joinRepository->userHasOtherWithStatus(
            applicationUserId: $flow->getUser()->getId(),
            excludeFlowId: $flow->getId(),
            status: OrganizationJoinStatusEnum::IN_PROGRESS,
        );
    }

    /**
     * @throws NonUniqueResultException
     */
    private function hasOrganization(OrganizationJoinFlow $flow): bool
    {
        return $this->organizationRepository->hasUserDefaultOrganization(
            $flow->getUser()->getUserIdentifier()
        );
    }

    /**
     * @throws NonUniqueResultException
     */
    public function startOrganizationJoinFlow(OrganizationJoinFlow $flow): void
    {
        if ($this->hasJoinFlowsInProgress($flow)){
            throw new BadRequestException('You already have task in progress');
        }

        if ($this->hasOrganization($flow)){
            throw new BadRequestException('You already have role in Organization');
        }

        $this->organizationJoinFlowStarter->startOrganizationJoinFlow($flow);
    }
}
