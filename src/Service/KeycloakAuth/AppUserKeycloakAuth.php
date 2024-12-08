<?php declare(strict_types=1);

namespace App\Service\KeycloakAuth;

use App\Service\KeycloakClient\Dto\RealmAuthDto;
use App\Service\KeycloakClient\Dto\RealmAuthInfo;
use App\Service\KeycloakClient\KeycloakClientInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\Serializer\SerializerInterface;

#[AsAlias(AppUserKeycloakAuthInterface::class)]
final readonly class AppUserKeycloakAuth implements AppUserKeycloakAuthInterface
{
    public function __construct(
        private KeycloakClientInterface $keycloakClient,
        private SerializerInterface $serializer
    )
    {
    }

    public function authUser(RealmAuthDto $dto): AuthUserResult
    {
        $keycloakAuthInfo = $this->keycloakClient->authUser($dto);

        return $this->serializer->deserialize(
            data: $this->getPayload($keycloakAuthInfo),
            type: AuthUserResult::class,
            format: 'json'
        );
    }

    private function getPayload(?RealmAuthInfo $keycloakAuthInfo): string
    {
        $tokenParts = explode('.', $keycloakAuthInfo->getAccessToken());

        return base64_decode($tokenParts[1]);
    }
}
