<?php declare(strict_types=1);

namespace App\Service\KeycloakAuth;

use Symfony\Component\Serializer\Annotation\SerializedName;

final class AuthUserResult
{
    public int $exp;
    public int $iat;
    public string $jti;
    public string $iss;
    #[SerializedName('sub')]
    public null|string $userIdentity = null;
    public string $typ;
    public string $azp;
    #[SerializedName('session_state')]
    public string $sessionState;
    public string $acr;
    public string $scope;
    public string $sid;
    #[SerializedName('email_verified')]
    public bool $emailVerified;
    public string|null $name = null;
    #[SerializedName('preferred_username')]
    public string $preferredUsername;
    #[SerializedName('given_name')]
    public string|null $givenName = null;
    #[SerializedName('family_name')]
    public string|null $familyName = null;
}
