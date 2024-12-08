<?php

namespace App\Service\KeycloakClient;

use App\Service\KeycloakClient\Dto\KeycloakUser;
use App\Service\KeycloakClient\Dto\RealmAuthDto;
use App\Service\KeycloakClient\Dto\RealmAuthInfo;
use App\Service\KeycloakClient\Dto\SearchUserDto;
use App\Service\KeycloakClient\Dto\UpdateUserPasswordDto;
use App\Service\KeycloakClient\Dto\ValidatePasswordDto;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class KeycloakClient implements KeycloakClientInterface
{
    private const VERIFY_PEER = false;
    private HttpClientInterface $httpClient;
    private KeycloakUrlProvider $keycloakConfig;

    public function __construct(
        #[Autowire('%env(KEYCLOAK_CLIENT_SECRET)%')]
        private readonly string                        $keycloakCLISecret,
        #[Autowire(env: 'KEYCLOAK_CLIENT_ID')]
        private readonly string                        $clientId,
        #[Autowire('%env(KEYCLOAK_REALM)%')]
        readonly string                                $keycloakRealmName,
        HttpClientInterface                            $httpClient,
        KeycloakUrlProvider                            $keycloakConfig,
        private readonly KeycloakMasterAdminAuthClient $keycloakMasterAdminAuthClient
    )
    {
        $this->keycloakConfig = $keycloakConfig;
        $this->httpClient = $httpClient;
    }

    public function authUser(RealmAuthDto $dto): ?RealmAuthInfo
    {
        $response = $this->httpClient->request(
            method: 'POST',
            url: $this->keycloakConfig->getRealmAuthUrl(),
            options: [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode("admin-cli:" . $this->keycloakCLISecret),
                ],
                'body' => [
                    'grant_type' => 'password',
                    'username' => $dto->username,
                    'password' => $dto->password,
                ],
                'verify_peer' => self::VERIFY_PEER,
                "verify_host" => self::VERIFY_PEER
            ],
        )->toArray();

        return RealmAuthInfo::fromArray($response);
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function refreshToken(string $refreshToken): array
    {
        return $this->httpClient->request(
            method: 'POST',
            url: $this->keycloakConfig->getRealmAuthUrl(),
            options: [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode("admin-cli:" . $this->keycloakCLISecret),
                ],
                'body' => [
                    'grant_type' => 'refresh_token',
                    'client_id' => $this->clientId,
                    'refresh_token' => $refreshToken,
                ],
                'verify_peer' => self::VERIFY_PEER,
                "verify_host" => self::VERIFY_PEER
            ],
        )->toArray();
    }

    public function logout(string $userId): void
    {
        $realmAuthInfo = $this->keycloakMasterAdminAuthClient->adminRealmAuth();

        $this->httpClient->request(
            method: 'POST',
            url: $this->keycloakConfig->getLogoutUrl($userId),
            options: [
                'headers' => [
                    'Authorization' => 'Bearer ' . $realmAuthInfo->getAccessToken(),
                    'Content-Type' => 'application/json',
                ],
                'verify_peer' => self::VERIFY_PEER,
                'verify_host' => self::VERIFY_PEER,
            ]
        );
    }

    public function getGroups(): array
    {
        return $this->get(
            url: $this->keycloakConfig->getGroupsUrl()
        )->toArray();
    }

    public function getRoles(): array
    {
        return $this->get(
            url: $this->keycloakConfig->getRolesUrl()
        )->toArray();
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getUsers(SearchUserDto $dto): array
    {
        return $this->get(
            url: $this->keycloakConfig->getUsersUrl($dto)
        )->toArray();
    }

    public function getUser(string $uuid): KeycloakUser
    {
        $data = $this->get(
            url: $this->keycloakConfig->getUserUrl($uuid)
        )->toArray();

        return KeycloakUser::fromArray($data);
    }

    public function getUserCredentials(string $uuid): array
    {
        return $this->get(
            url: $this->keycloakConfig->getUserCredentialsUrl($uuid)
        )->toArray();
    }

    public function resetUserPassword(UpdateUserPasswordDto $dto): void
    {
        $this->put(
            url: $this->keycloakConfig->resetUserPasswordUrl($dto->uuid),
            data: [
                'type' => 'password',
                'value' => $dto->password,
                'temporary' => $dto->temporary
            ]
        );
    }

    public function validatePassword(ValidatePasswordDto $dto): bool
    {
        $user = $this->getUser($dto->uuid);
        $authDto = new RealmAuthDto();
        $authDto->username = $user->username;
        $authDto->password = $dto->password;
        try {
            $this->authUser($authDto);
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    private function get(string $url): ResponseInterface
    {
        return $this->request(
            method: 'GET',
            url: $url,
        );
    }

    private function put(string $url, array|object $data = []): ResponseInterface
    {
        return $this->request(
            method: 'PUT',
            url: $url,
            jsonData: $data
        );
    }

    private function request(string $method, string $url, array $jsonData = [], $isAuth = true): ResponseInterface
    {
        return $this->httpClient->request(
            method: $method,
            url: $url,
            options: $this->getOptions($jsonData, $isAuth)
        );
    }

    private function getOptions(array $jsonData, bool $isAuth): array
    {
        $options = [
            'verify_peer' => self::VERIFY_PEER,
            "verify_host" => self::VERIFY_PEER
        ];

        if ($isAuth) {
            $realmAuthInfo = $this->keycloakMasterAdminAuthClient->adminRealmAuth();
            $options['headers'] = [
                'Authorization' => $realmAuthInfo->getAuthToken()
            ];
        }
        if ($jsonData) {
            $options['json'] = $jsonData;
        }

        return $options;
    }
}
