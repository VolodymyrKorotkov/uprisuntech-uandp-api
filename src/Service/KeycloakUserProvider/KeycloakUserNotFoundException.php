<?php declare(strict_types=1);

namespace App\Service\KeycloakUserProvider;

use Throwable;

final class KeycloakUserNotFoundException extends \Exception
{
    public function __construct(string $userIdentity, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('User not found by ' . $userIdentity.' on keycloak', $code, $previous);
    }
}
