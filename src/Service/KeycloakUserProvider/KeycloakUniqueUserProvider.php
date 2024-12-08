<?php

namespace App\Service\KeycloakUserProvider;

use App\Service\KeycloakClient\Dto\KeycloakUser;
use App\Service\KeycloakClient\Dto\SearchUserDto;
use App\Service\KeycloakClient\KeycloakAdminClientInterface;

final readonly class KeycloakUniqueUserProvider implements KeycloakUniqueUserProviderInterface
{
    public function __construct(
        private KeycloakAdminClientInterface $userClient
    )
    {}

    /**
     * @throws KeycloakUserNotFoundException
     */
    public function getByEmail(string $email): KeycloakUser
    {
        $search = new SearchUserDto();
        $search->email = $email;

        $users = $this->userClient->getUsers($search);
        if ($users->isEmpty()){
            throw new KeycloakUserNotFoundException($email);
        }

        return $users->getFirst();
    }

    public function getByUsername(string $username): KeycloakUser
    {
        $search = new SearchUserDto();
        $search->username = $username;

        $users = $this->userClient->getUsers($search);
        if ($users->isEmpty()){
            throw new KeycloakUserNotFoundException($username);
        }

        return $users->getFirst();
    }

    public function getByIdentity(string $identity): KeycloakUser
    {
        return $this->userClient->getUser($identity);
    }
}
