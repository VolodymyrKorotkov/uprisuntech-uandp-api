<?php declare(strict_types=1);

namespace App\Controller\ApiPlatform\ConfirmEmail;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Controller\ApiPlatform\ConfirmEmail\Dto\ConfirmEmailDto;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final readonly class ConfirmEmailProcessor implements ProcessorInterface
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $this->doProcess($data);
    }

    private function doProcess(ConfirmEmailDto $dto): void
    {
        $user = $this->findUser($dto) ?? throw new AccessDeniedException();
        $user->setIsVerifiedEmail(
            true
        );
        $user->setConfirmEmailHash(null);
        $this->userRepository->save($user);
    }

    /**
     * @param ConfirmEmailDto $dto
     * @return object|null
     */
    private function findUser(ConfirmEmailDto $dto): ?User
    {
        return $this->userRepository->findOneBy(['confirmEmailHash' => $dto->confirmEmailHash]);
    }
}
