<?php

namespace App\Service\OrganizationJoinInviteProcessor;

use App\Entity\OrganizationUserRole;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsDecorator(OrganizationJoinInviteProcessorInterface::class, priority: self::VALIDATE_DRFO_CODE_PRIORITY)]
final readonly class ValidateDrfoCodeDecorator implements OrganizationJoinInviteProcessorInterface
{
    const DRFO_CODE_NOT_MATCH_MESSAGE = 'DRFO code not match';

    public function __construct(
        private OrganizationJoinInviteProcessorInterface $inviteProcessor,
        private UserRepository $userRepository,
    )
    {}

    /**
     * @throws EntityNotFoundException
     */
    public function processOrganizationJoinInvite(ProcessOrganizationJoinInviteDto $dto): OrganizationUserRole
    {
        $user = $this->userRepository->getByUserIdentity($dto->userIdentity);
        if ($user->getDrfoCode() === $dto->invite->getDrfoCode()){
            return $this->inviteProcessor->processOrganizationJoinInvite($dto);
        }

        throw new BadRequestHttpException(self::DRFO_CODE_NOT_MATCH_MESSAGE);
    }
}
