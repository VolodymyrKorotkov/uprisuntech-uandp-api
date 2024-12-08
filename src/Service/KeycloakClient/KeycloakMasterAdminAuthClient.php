<?php declare(strict_types=1);

namespace App\Service\KeycloakClient;

use App\Service\KeycloakClient\Dto\RealmAuthInfo;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class KeycloakMasterAdminAuthClient
{
    private const VERIFY_PEER = false;

    public function __construct(
        #[Autowire('%env(KEYCLOAK_ADMIN)%')]
        private string                 $keycloakAdminUsername,
        #[Autowire('%env(KEYCLOAK_ADMIN_PASSWORD)%')]
        private string                 $keycloakAdminPassword,
        #[Autowire('%env(KEYCLOAK_API_URL)%')]
        private string                 $keycloakUrl,
        private CacheItemPoolInterface $cache,
        private HttpClientInterface    $client,
    )
    {
    }

    // todo: return cache and fix 401

    /**
     * @return RealmAuthInfo
     * @throws ClientExceptionInterface
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function adminRealmAuth(): RealmAuthInfo
    {
        return $this->doAuthAdmin();
        $cacheItem = $this->cache->getItem('realmAuthInfo');

        if (!$cacheItem->isHit()) {
            $realmAuthInfo = $this->doAuthAdmin();

            $cacheItem->expiresAfter(
                new \DateInterval("PT{$realmAuthInfo->getExpiresIn()}S")
            );
            $cacheItem->set($realmAuthInfo);
            $this->cache->save($cacheItem);
        } else {
            $realmAuthInfo = $cacheItem->get();
        }

        return $realmAuthInfo;
    }

    /**
     * @return RealmAuthInfo
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function doAuthAdmin(): RealmAuthInfo
    {
        $response = $this->client->request(
            method: 'POST',
            url: $this->keycloakUrl . '/realms/master/protocol/openid-connect/token',
            options: [
//                'headers' => [
//                    'Authorization' => 'Basic ' . base64_encode("admin-cli:" . $this->keycloakCLISecret),
//                ],
                'body' => [
                    'client_id' => 'admin-cli',
                    'grant_type' => 'password',
                    'username' => $this->keycloakAdminUsername,
                    'password' => $this->keycloakAdminPassword,
                ],
                'verify_peer' => self::VERIFY_PEER,
                "verify_host" => self::VERIFY_PEER
            ],
        );

        return RealmAuthInfo::fromArray(
            json_decode($response->getContent(), true)
        );
    }
}
