<?php

namespace App\Controller\ApiPlatform\UserByDrfo;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Controller\ApiPlatform\UserByDrfo\Dto\DrfoCodeDto;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityNotFoundException;

class GetUserByDrfoProcessor implements ProcessorInterface
{

    public function __construct(
        private UserRepository $userRepository
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        return $this->getUser($data);
    }

    /**
     * @throws EntityNotFoundException
     */
    private function getUser(DrfoCodeDto $dto)
    {
        return $this->userRepository->findByDrfoCode($dto->drfoCode);
    }
}