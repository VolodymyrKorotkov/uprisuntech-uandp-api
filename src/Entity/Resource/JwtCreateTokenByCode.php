<?php declare(strict_types=1);

namespace App\Entity\Resource;

final readonly class JwtCreateTokenByCode
{
    public function __construct(
        public string $code
    )
    {}
}
