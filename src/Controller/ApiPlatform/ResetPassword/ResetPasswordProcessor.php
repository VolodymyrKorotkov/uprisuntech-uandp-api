<?php declare(strict_types=1);

namespace App\Controller\ApiPlatform\ResetPassword;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Controller\ApiPlatform\ResetPassword\Dto\ResetPasswordDto;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserPasswordUpdater\Dto\UpdatePasswordDto;
use App\Service\UserPasswordUpdater\UserPasswordUpdaterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final readonly class ResetPasswordProcessor implements ProcessorInterface
{
    private UserRepository $userRepository;
    private UserPasswordUpdaterInterface $userPasswordUpdater;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordUpdaterInterface $userPasswordUpdater
    )
    {
        $this->userRepository = $userRepository;
        $this->userPasswordUpdater = $userPasswordUpdater;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $this->doProcess($data);
    }

    private function doProcess(ResetPasswordDto $dto): void
    {
        $user = $this->findUser($dto) ?? throw new AccessDeniedException();
        $this->userPasswordUpdater->updateUserPassword(
            new UpdatePasswordDto(
                identifier: $user->getUserIdentifier(),
                password: $dto->password
            )
        );
    }

    /**
     * @param ResetPasswordDto $dto
     * @return object|null
     */
    private function findUser(ResetPasswordDto $dto): ?User
    {
        return $this->userRepository->findOneBy(['resetPasswordHash' => $dto->resetPasswordHash]);
    }
}
