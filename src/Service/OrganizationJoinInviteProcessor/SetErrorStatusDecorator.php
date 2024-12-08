<?php declare(strict_types=1);

namespace App\Service\OrganizationJoinInviteProcessor;

use App\Entity\OrganizationUserRole;
use App\Enum\OrganizationJoinInviteStatusEnum;
use App\Repository\OrganizationJoinInviteRepository;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Throwable;

#[AsDecorator(OrganizationJoinInviteProcessorInterface::class, priority: self::SET_ERROR_PRIORITY)]
final readonly class SetErrorStatusDecorator implements OrganizationJoinInviteProcessorInterface
{
    public function __construct(
        private OrganizationJoinInviteProcessorInterface $inviteProcessor,
        private OrganizationJoinInviteRepository $inviteRepository
    )
    {}

    /**
     * @throws Throwable
     */
    public function processOrganizationJoinInvite(ProcessOrganizationJoinInviteDto $dto): OrganizationUserRole
    {
        try {
            return $this->inviteProcessor->processOrganizationJoinInvite($dto);
        } catch (BadRequestHttpException $throwable){
            $invite = $dto->invite;
            $invite->setStatus(OrganizationJoinInviteStatusEnum::ERROR);
            $invite->setComment($throwable->getMessage());

            $this->inviteRepository->save($invite);

            throw $throwable;
        }
    }
}
