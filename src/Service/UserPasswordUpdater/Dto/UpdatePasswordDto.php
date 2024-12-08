<?php

namespace App\Service\UserPasswordUpdater\Dto;

class UpdatePasswordDto
{
    public string $identifier;
    public string $password;

    public function __construct(string $identifier, string $password)
    {
        $this->identifier = $identifier;
        $this->password = $password;
    }
}