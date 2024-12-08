<?php

namespace App\Service\UserPasswordUpdater;

use App\Service\KeycloakClient\Dto\UpdateUserPasswordDto;
use App\Service\KeycloakClient\KeycloakClientInterface;
use App\Service\UserPasswordUpdater\Dto\UpdatePasswordDto;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserPasswordUpdater implements  UserPasswordUpdaterInterface
{
    private readonly KeycloakClientInterface $keycloakClient;

    public function __construct(
        KeycloakClientInterface $keycloakClient
    )
    {
        $this->keycloakClient = $keycloakClient;
    }

    public function updateUserPassword(UpdatePasswordDto $dto): void
    {
        if (count($this->keycloakClient->getUserCredentials($dto->identifier))) {
            $updatePassword = new UpdateUserPasswordDto();
            $updatePassword->password = $dto->password;
            $updatePassword->uuid = $dto->identifier;
            $this->keycloakClient->resetUserPassword($updatePassword);
        } else {
            throw new BadRequestHttpException('Password cannot be reset.');
        }
    }
}