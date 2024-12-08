<?php declare(strict_types=1);

namespace App\Service\FormIoClient;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

final readonly class FormioProxyClient
{
    public function __construct(
        #[Autowire(env: 'FORMIO_PROJECT_URL')]
        private string                   $formioApiUrl,
        private HttpClientInterface      $client,
        private Security                 $security,
        private AuthUserJwtTokenProvider $jwtTokenProvider
    )
    {
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws Throwable
     */
    public function proxyRequest(Request $request): Response
    {
        $url = $this->formioApiUrl . '/' . implode('/', $request->attributes->all('_route_params'));
        if ($request->getQueryString()){
            $url .= '?'.$request->getQueryString();
        }

        $options = [
            'headers' => $this->getHeaders($request)
        ];
        if ($request->getContent()) {
            $options['json'] = $request->toArray();
        }

        $formioResponse = $this->client->request(
            $request->getMethod(),
            $url,
            options: $options
        );

        return new JsonResponse(
            data: $formioResponse->getContent(false),
            status: $formioResponse->getStatusCode(),
            headers: $formioResponse->getHeaders(false),
            json: true
        );
    }

    /**
     * @param Request $request
     * @return null[]|\null[][]|string[]|string[][]
     * @throws Throwable
     */
    private function getHeaders(Request $request): array
    {
        $headers = $request->headers->all();
        if ($this->security->getUser()) {
            $headers['X-Jwt-Token'][] = $this->jwtTokenProvider->getJwtToken();
        }

        return $headers;
    }
}
