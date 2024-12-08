<?php

namespace App\Service\OrganizationJoinInviteProcessor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\OrganizationUserRole;
use App\Enum\OrganizationJoinInviteStatusEnum;
use App\Repository\OrganizationJoinInviteRepository;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly final class InviteUserStateProcessor implements ProcessorInterface
{
    public function __construct(
        private OrganizationJoinInviteRepository $organizationJoinInviteRepository,
        private OrganizationJoinInviteProcessorInterface $inviteProcessor,
        private Security $security
    )
    {}

    /**
     * @throws EntityNotFoundException
     */
    public function processInviteUserState(ProcessInviteUserStateDto $dto): OrganizationUserRole
    {
        $invite = $this->organizationJoinInviteRepository->getByUserState($dto->state);

        if ($invite->getStatus() !== OrganizationJoinInviteStatusEnum::INVITED) {
            throw new NotFoundHttpException('Invite is not active');
        }

        return $this->inviteProcessor->processOrganizationJoinInvite(
            new ProcessOrganizationJoinInviteDto(
                invite: $invite,
                userIdentity: $this->security->getUser()->getUserIdentifier()
            )
        );
    }

    /**
     * @throws EntityNotFoundException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): OrganizationUserRole
    {
       return $this->processInviteUserState($data);
    }
}
