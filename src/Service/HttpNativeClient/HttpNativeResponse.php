<?php declare(strict_types=1);

namespace App\Service\HttpNativeClient;

use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final readonly class HttpNativeResponse
{
    public function __construct(
        private ResponseInterface   $response,
        private SerializerInterface $serializer,
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function deserializeResponse(string $type)
    {
        try {
            return $this->serializer->deserialize(
                data: $this->response->getContent(),
                type: $type,
                format: 'json'
            );
        } catch (ClientException|ServerException $clientException){
            throw new BadRequestHttpException($clientException->getResponse()->getContent(false), $clientException);
        }
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
