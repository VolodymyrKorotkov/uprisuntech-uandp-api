<?php

namespace App\Security\UserToken;

use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

readonly class UserTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken): UserBadge
    {
        $user = $this->userRepository->findByToken($accessToken);
        if (!$user) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        return new UserBadge($user->getUserIdentifier());
    }
}
