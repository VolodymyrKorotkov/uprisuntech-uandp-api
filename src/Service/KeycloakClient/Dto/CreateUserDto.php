<?php

namespace App\Service\KeycloakClient\Dto;

use App\Entity\User;

class CreateUserDto
{
    public function __construct(
        public ?string $email = null,
        public ?string $password = null,
        public ?string $username = null,
        public ?string $firstName = null,
        public ?string $lastName = null,
        public array   $attributes = [],
    )
    {
    }

    public static function newFromUser(User $user, string $username): CreateUserDto
    {
        $res = new self();

        $res->email = $user->getEmail();
        $res->firstName = $user->getName();
        $res->lastName = $user->getLastname();
        $res->username = $username;

        return $res;
    }
}
