<?php declare(strict_types=1);

namespace App\Entity\Resource;

final readonly class JwtCreateTokenByCredentials
{
    public function __construct(
        public string $email,
        public string $password
    )
    {}
}
