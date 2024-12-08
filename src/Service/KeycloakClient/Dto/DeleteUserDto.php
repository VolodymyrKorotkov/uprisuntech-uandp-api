<?php declare(strict_types=1);

namespace App\Service\KeycloakClient\Dto;

use App\Entity\User;

final readonly class DeleteUserDto
{
    public function __construct(
        public string $uuid
    )
    {}

    public static function newFromUser(User $user): DeleteUserDto
    {
        return new self(
            uuid: $user->getUserIdentifier()
        );
    }
}
