<?php
declare(strict_types=1);

namespace App\Controller\ApiPlatform\EmailPasswordInit;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Controller\ApiPlatform\EmailPasswordInit\Dto\EmailPasswordInitDto;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserPasswordUpdater\Dto\UpdatePasswordDto;
use App\Service\UserPasswordUpdater\UserPasswordUpdaterInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class EmailPasswordInitProcessor implements ProcessorInterface
{
    public function __construct(
        private UserRepository               $userRepository,
        private Security                     $security,
        private UserPasswordUpdaterInterface $userPasswordUpdater
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $this->doProcess($data);
    }

    private function doProcess(EmailPasswordInitDto $dto): void
    {
        $currentUser = $this->getAuthenticatedUser();
        $user = $this->getUserByIdentifier($currentUser->getUserIdentifier());


        $this->updateEmailAndPassword($user, $dto);

        $this->userRepository->save($user);
    }

    private function getAuthenticatedUser(): UserInterface
    {
        return $this->security->getUser();
    }

    private function getUserByIdentifier(string $username): User
    {
        return $this->userRepository->findByUserIdentity($username);
    }

    private function updateEmailAndPassword(User $user, EmailPasswordInitDto $dto): void
    {
        $user->setEmail($dto->email);
        $user->setHasPassword(true);
        $this->userPasswordUpdater->updateUserPassword(
            new UpdatePasswordDto(
                identifier: $user->getUserIdentifier(),
                password: $dto->password
            )
        );
    }
}
