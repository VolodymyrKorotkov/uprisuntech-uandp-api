<?php

namespace App\Security;

use App\Enum\UserRoleEnum;
use App\Repository\UserJwtTokenRepository;
use App\Service\AppUserProvider;
use App\Service\DefaultOrganizationProviderInterface;
use App\Service\KeycloakClient\KeycloakAdminClientInterface;
use App\Service\KeycloakClient\KeycloakClientInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[AsAlias('app.security.keycloak_user_provider')]
final readonly class KeycloakSecurityUserProvider implements UserProviderInterface
{
    public function __construct(
        private KeycloakAdminClientInterface $keycloakAdminClient,
        private KeycloakClientInterface $keycloakClient,
        private DefaultOrganizationProviderInterface $defaultOrganizationProvider,
        private AppUserProvider $userProvider,
        private JWTTokenManagerInterface $JWTTokenManager,
        private UserJwtTokenRepository $jwtTokenRepository
    )
    {}

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws JWTDecodeFailureException
     * @throws ClientExceptionInterface
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $this->refreshJwtToken($identifier);

        return new AppUser(
            $identifier,
            $this->getRoles($identifier)
        );
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws JWTDecodeFailureException
     * @throws ClientExceptionInterface
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof AppUser) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return AppUser::class === $class;
    }

    /**
     * @param string $identifier
     * @return array
     */
    private function getRoles(string $identifier): array
    {
        $keycloakRoles = $this->keycloakAdminClient->getUserRolesRealmMapping($identifier);

        $roles = array_map(fn($item) => $item['name'], $keycloakRoles);
        $roles[] = UserRoleEnum::ROLE_USER_CASE->value;

        try {
            $organizationRole = $this->defaultOrganizationProvider->getDefaultOrganizationRole($identifier);
            $roles[] = $organizationRole->getRole()->value;
        } catch (NotFoundHttpException) {
            $roles[] = UserRoleEnum::ROLE_WITHOUT_ORGANIZATION->value;
        }

        return array_values(
            array_unique($roles)
        );
    }

    /**
     * @param string $identifier
     * @return void
     * @throws JWTDecodeFailureException
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function refreshJwtToken(string $identifier): void
    {
        $user = $this->userProvider->getUser($identifier);
        if ($user->getJwtTokens()->isEmpty()){
            return;
        }

        $jwt = $user->getLastJwtToken();

        try {
            $this->JWTTokenManager->parse($jwt->getJwtToken());
        } catch (JWTDecodeFailureException $exception) {
            if ($exception->getMessage() !== 'Expired JWT Token') {
                throw $exception;
            }

            try {
                $respData = $this->keycloakClient->refreshToken($jwt->getRefreshToken());
                $jwt->setJwtToken($respData['access_token']);
                $jwt->setRefreshToken($respData['refresh_token']);

                $this->jwtTokenRepository->save($jwt);
            } catch (ClientException $throwable) {
                $this->jwtTokenRepository->remove($jwt);
                throw new AuthenticationException($throwable->getMessage() . ': ' . $throwable->getResponse()->getContent(false));
            }
        }
    }
}
