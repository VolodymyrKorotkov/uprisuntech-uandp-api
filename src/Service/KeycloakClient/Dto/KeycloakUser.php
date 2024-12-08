<?php

namespace App\Service\KeycloakClient\Dto;

class KeycloakUser
{
    public string $id;
    public int $createdTimestamp;
    public ?string $username = null;
    public ?bool $enabled = null;
    public ?bool $totp = null;
    public ?bool $emailVerified = null;
    public ?string $firstName = null;
    public ?string $lastName = null;
    public ?string $email = null;
    public ?array $disableableCredentialTypes = null;
    public ?array $requiredActions = null;
    public ?int $notBefore = null;
    public ?array $access = null;

    static function fromArray(array $data): KeycloakUser
    {
        $user = new self();
        $user->id = $data['id'];
        $user->createdTimestamp = $data['createdTimestamp'];
        $user->username = $data['username'];
        $user->enabled = $data['enabled'];
        $user->totp = $data['totp'];
        $user->emailVerified = $data['emailVerified'];
        $user->firstName = $data['firstName'];
        $user->lastName = $data['lastName'];
        $user->email = $data['email'];
        $user->disableableCredentialTypes = $data['disableableCredentialTypes'];
        $user->requiredActions = $data['requiredActions'];
        $user->notBefore = $data['notBefore'];
        $user->access = $data['access'];

        return $user;
    }
}
