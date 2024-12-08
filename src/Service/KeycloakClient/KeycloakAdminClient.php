<?php declare(strict_types=1);

namespace App\Service\KeycloakClient;

use App\Enum\UserRoleEnum;
use App\Service\KeycloakClient\Dto\CreateUserDto;
use App\Service\KeycloakClient\Dto\DeleteUserDto;
use App\Service\KeycloakClient\Dto\KeycloakRequest;
use App\Service\KeycloakClient\Dto\KeycloakUser;
use App\Service\KeycloakClient\Dto\KeycloakUserCollection;
use App\Service\KeycloakClient\Dto\SearchUserDto;
use App\Service\KeycloakClient\Dto\UpdateUserDto;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

#[AsAlias(KeycloakAdminClientInterface::class)]
final readonly class KeycloakAdminClient implements KeycloakAdminClientInterface
{
    public function __construct(
        private KeycloakMasterAdminAuthClient     $authClient,
        private KeycloakCommonHttpClientInterface $keycloakCommonHttpClient,
        private SerializerInterface               $serializer
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws InvalidArgumentException
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getUsers(SearchUserDto $dto): KeycloakUserCollection
    {
        $usersResponse = $this->keycloakCommonHttpClient->request(
            new KeycloakRequest(
                method: 'GET',
                path: KeycloakCommonHttpClientInterface::GET_USERS,
                authUser: $this->authClient->adminRealmAuth(),
                queryParams: $dto
            )
        );

        return $this->getKeycloakUserCollection($usersResponse);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws InvalidArgumentException
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getUser(string $identity): KeycloakUser
    {
        $usersResponse = $this->keycloakCommonHttpClient->request(
            new KeycloakRequest(
                method: 'GET',
                path: KeycloakCommonHttpClientInterface::GET_USER,
                authUser: $this->authClient->adminRealmAuth(),
                parameters: ['uuid' => $identity]
            )
        );

        return $this->serializer->deserialize(
            data: $usersResponse->getContent(),
            type: KeycloakUser::class,
            format: 'json'
        );
    }

    /**
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function deleteUser(DeleteUserDto $dto): void
    {
        $this->keycloakCommonHttpClient->request(
            new KeycloakRequest(
                method: 'DELETE',
                path: KeycloakCommonHttpClientInterface::DELETE_USER,
                authUser: $this->authClient->adminRealmAuth(),
                parameters: $dto
            )
        );
    }

    /**
     * @throws TransportExceptionInterface
     * @throws InvalidArgumentException
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getUsersByRole(UserRoleEnum $roleEnum): KeycloakUserCollection
    {
        $usersResponse = $this->keycloakCommonHttpClient->request(
            new KeycloakRequest(
                method: 'GET',
                path: KeycloakCommonHttpClientInterface::GET_USERS_BY_ROLE,
                authUser: $this->authClient->adminRealmAuth(),
                parameters: [
                    'roleName' => $roleEnum->value
                ]
            )
        );

        return $this->getKeycloakUserCollection($usersResponse);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws InvalidArgumentException
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function updateUser(UpdateUserDto $dto): void
    {
        $this->keycloakCommonHttpClient->request(
            new KeycloakRequest(
                method: 'PUT',
                path: KeycloakCommonHttpClientInterface::UPDATE_USER,
                authUser: $this->authClient->adminRealmAuth(),
                parameters: ['uuid' => $dto->userIdentifier],
                body: $dto->getRequestBody()
            )
        );
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function createUser(CreateUserDto $dto): void
    {
        $body = [
            'username' => $dto->email,
            'enabled' => true,
            'firstName' => $dto->firstName,
            'lastName' => $dto->lastName,
            'email' => $dto->email,
        ];
        if ($dto?->password) {
            $body['credentials'] = [[
                'algorithm' => 'bcrypt',
                'type' => 'password',
                'value' => $dto->password,
                'temporary' => false
            ]];
        }

        if ($dto?->username) {
            $body['username'] = $dto->username;
        }
        if (count($dto->attributes)) {
            $body['attributes'] = $dto->attributes;
        }

        $this->keycloakCommonHttpClient->request(
            new KeycloakRequest(
                method: 'POST',
                path: KeycloakCommonHttpClientInterface::CREATE_USER,
                authUser: $this->authClient->adminRealmAuth(),
                body: $body
            )
        );
    }

    /**
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function getUserRolesRealmMapping(string $uuid): array
    {
        return $this->keycloakCommonHttpClient->request(
            new KeycloakRequest(
                method: 'GET',
                path: KeycloakCommonHttpClientInterface::GET_USER_ROLES,
                authUser: $this->authClient->adminRealmAuth(),
                parameters: [
                    'uuid' => $uuid
                ]
            )
        )->toArray()['realmMappings'] ?? [];
    }

    /**
     * @throws TransportExceptionInterface
     * @throws InvalidArgumentException
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function updateUserRolesRealmMapping(string $uuid, array $roles): void
    {
        $this->keycloakCommonHttpClient->request(
            new KeycloakRequest(
                method: 'POST',
                path: KeycloakCommonHttpClientInterface::UPDATE_USER_ROLES,
                authUser: $this->authClient->adminRealmAuth(),
                parameters: [
                    'uuid' => $uuid
                ],
                body: $roles
            )
        );
    }

    /**
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function getRoleByName(string $roleName): array
    {
        return $this->keycloakCommonHttpClient->request(
            new KeycloakRequest(
                method: 'GET',
                path: KeycloakCommonHttpClientInterface::GET_ROLE_BY_NAME,
                authUser: $this->authClient->adminRealmAuth(),
                parameters: [
                    'roleName' => $roleName
                ]
            )
        )->toArray();
    }

    /**
     * @param ResponseInterface $usersResponse
     * @return KeycloakUserCollection
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function getKeycloakUserCollection(ResponseInterface $usersResponse): KeycloakUserCollection
    {
        return new KeycloakUserCollection(
            items: $this->serializer->deserialize(
                data: $usersResponse->getContent(),
                type: KeycloakUser::class . '[]',
                format: 'json'
            )
        );
    }
}
