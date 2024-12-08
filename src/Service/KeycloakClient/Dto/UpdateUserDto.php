<?php

namespace App\Service\KeycloakClient\Dto;

use App\Entity\User;

class UpdateUserDto
{
    public string $userIdentifier;
    public ?string $email = null;
    public ?bool $emailVerified = null;
    public ?string $firstName;
    public ?string $lastName;

    public static function newFromUser(User $user): UpdateUserDto
    {
        $res = new self();
        $res->userIdentifier = $user->getUserIdentifier();
        $res->email = $user->getEmail();
        $res->emailVerified = $user->isIsVerifiedEmail();
        $res->lastName = $user->getLastname();
        $res->firstName = $user->getName();

        return $res;
    }

    public function getRequestBody(): array
    {
        return [
            'email' => $this->email,
            'emailVerified' => $this->emailVerified,
            'lastName' => $this->lastName,
            'firstName' => $this->firstName
        ];
    }
}

