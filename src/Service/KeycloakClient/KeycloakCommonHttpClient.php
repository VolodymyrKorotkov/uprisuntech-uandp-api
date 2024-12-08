<?php

namespace App\Service\KeycloakClient;

use App\Serializer\AppJsonCopyNormalizerInterface;
use App\Service\KeycloakClient\Dto\KeycloakRequest;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

#[AsAlias(KeycloakCommonHttpClientInterface::class)]
final readonly class KeycloakCommonHttpClient implements KeycloakCommonHttpClientInterface
{
    private const VERIFY_PEER = false;

    public function __construct(
        #[Autowire('%env(KEYCLOAK_REALM)%')]
        private string $realm,
        #[Autowire('%env(KEYCLOAK_API_URL)%')]
        private string $keycloakUrl,
        private HttpClientInterface $client,
        private AppJsonCopyNormalizerInterface $appJsonNormalizer
    )
    {}

    /**
     * @param KeycloakRequest $request
     * @return ResponseInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function request(KeycloakRequest $request): ResponseInterface
    {
        try {
            return $this->client->request(
                method: $request->method,
                url: $this->getUrl($request),
                options: $this->getOptions($request)
            );
        } catch (ClientException $clientException){
            $response = $clientException->getResponse();
            if ($response->getStatusCode() >= 400 && $response->getStatusCode() < 500){
                throw new BadRequestException(
                    $clientException->getMessage().': '.$response->getContent(false)
                );
            } else {
                throw $clientException;
            }
        }
    }

    private function getUrl(KeycloakRequest $request): string
    {
        $url = $this->keycloakUrl . str_replace('{realm}', $this->realm, $request->path);

        if ($request->hasParameters()){
            $parameters = $this->appJsonNormalizer->normalize($request->parameters);
            foreach ($parameters as $name => $value){
                $url = str_replace('{'.$name.'}', $value, $url);
            }
        }

        if ($request->hasQueryParams()){
            $url .= '?';
            $url .= http_build_query($this->appJsonNormalizer->normalize($request->queryParams));
        }

        return $url;
    }

    private function getOptions(KeycloakRequest $request): array
    {
        $options = [
            'verify_peer' => self::VERIFY_PEER,
            "verify_host" => self::VERIFY_PEER,
            'headers' => ['Authorization' => $request->authUser->getAuthToken()]
        ];

        if ($request->hasBody()) {
            $options[$request->isJsonContent ? 'json' : 'body'] = $this->appJsonNormalizer->normalize($request->body);
        }

        return $options;
    }
}
