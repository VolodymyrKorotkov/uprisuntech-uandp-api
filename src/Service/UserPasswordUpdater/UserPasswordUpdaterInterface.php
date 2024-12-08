<?php

namespace App\Service\UserPasswordUpdater;

use App\Service\UserPasswordUpdater\Dto\UpdatePasswordDto;

interface UserPasswordUpdaterInterface
{
    public function updateUserPassword(UpdatePasswordDto $dto): void;
}