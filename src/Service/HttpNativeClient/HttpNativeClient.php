<?php declare(strict_types=1);

namespace App\Service\HttpNativeClient;

use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

final readonly class HttpNativeClient
{
    public function __construct(
        private HttpClientInterface $formioClient,
        private SerializerInterface $serializer,
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws ExceptionInterface
     * @throws Throwable
     */
    public function post(string $url, array $params = [], array|object $data = [], array $headers = []): HttpNativeResponse
    {
        try {
            $response = $this->formioClient->request(
                method: 'POST',
                url: $this->getUrl($url, $params),
                options: [
                    'headers' => $headers,
                    'json' => $this->serializer->normalize($data)
                ]
            );
        } catch (ClientException $e) {
            throw $this->getCustomException($e);
        }

        return new HttpNativeResponse($response, $this->serializer);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws Throwable
     */
    public function get(string $url, array $params = [], array $headers = []): HttpNativeResponse
    {
        $url = $this->getUrl($url, $params);

        try {
            $response = $this->formioClient->request(
                method: 'GET',
                url: $url,
                options: [
                    'headers' => $headers,
                ]
            );
        } catch (ClientException $e) {
            throw $this->getCustomException($e);
        }

        return new HttpNativeResponse($response, $this->serializer);
    }

    /**
     * @throws Throwable
     */
    public function put(string $url, array $params, array|object $data = [], array $headers = []): HttpNativeResponse
    {
        try {
            $response = $this->formioClient->request(
                method: 'PUT',
                url: $this->getUrl($url, $params),
                options: [
                    'headers' => $headers,
                    'json' => $this->serializer->normalize($data)
                ]
            );
        } catch (ClientException $e) {
            throw $this->getCustomException($e);
        }

        return new HttpNativeResponse($response, $this->serializer);
    }

    /**
     * @throws Throwable
     */
    private function getCustomException(ClientException $e): Throwable
    {
        if ($this->is400StatusError($e)) {
            return new BadRequestHttpException('Formio submission: ' . $e->getResponse()->getContent(false), $e);
        } else {
            return $e;
        }
    }

    /**
     * @param ClientException $e
     * @return bool
     * @throws TransportExceptionInterface
     */
    private function is400StatusError(ClientException $e): bool
    {
        return $e->getResponse()->getStatusCode() >= 400 && $e->getResponse()->getStatusCode() <= 500;
    }

    private function getUrl(string $url, array $params): string
    {
        if ($params) {
            $url = str_replace(array_keys($params), array_values($params), $url);
        }

        return $url;
    }
}
