<?php declare(strict_types=1);

namespace App\Controller\ApiPlatform;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Controller\ApiPlatform\UpdatePassword\Dto\UpdatePassword;
use App\Repository\UserRepository;
use App\Service\UserPasswordUpdater\Dto\UpdatePasswordDto;
use App\Service\UserPasswordUpdater\UserPasswordUpdaterInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final readonly class UpdatePasswordProcessor implements ProcessorInterface
{
    private UserRepository $userRepository;
    private Security $security;
    private UserPasswordUpdaterInterface $userPasswordUpdater;

    public function __construct(
        UserRepository              $userRepository,
        Security                    $security,
        UserPasswordUpdaterInterface $userPasswordUpdater
    )
    {
        $this->userRepository = $userRepository;
        $this->security = $security;
        $this->userPasswordUpdater = $userPasswordUpdater;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $this->doProcess($data);
    }

    private function doProcess(UpdatePassword $dto): void
    {
        $user = $this->userRepository->findByUserIdentity($this->security->getUser()->getUserIdentifier());
        if (!$user) {
            throw new AccessDeniedException('User not found');
        }
        $this->userPasswordUpdater->updateUserPassword(
            new UpdatePasswordDto(
                identifier: $user->getUserIdentifier(),
                password: $dto->newPassword
            )
        );
    }
}
