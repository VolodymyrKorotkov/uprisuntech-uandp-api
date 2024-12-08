<?php declare(strict_types=1);

namespace App\Service\OauthUserProvider\Dto;

use App\Entity\User;

final readonly class GetOauthUserResult
{
    public function __construct(
        public bool $isNewUser,
        public User $user,
    )
    {}
}
