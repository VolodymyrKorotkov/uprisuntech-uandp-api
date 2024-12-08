<?php

namespace App\Service\KeycloakClient\Dto;

class UpdateUserPasswordDto
{
    public string $uuid;
    public string $password;
    public bool $temporary = false;
}