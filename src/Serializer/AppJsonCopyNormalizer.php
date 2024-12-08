<?php

namespace App\Serializer;

use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

final class AppJsonCopyNormalizer implements AppJsonCopyNormalizerInterface
{
    private SerializerInterface|Serializer $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function denormalize(mixed $data, string $type, array $context = []): mixed
    {
        return $this->serializer->denormalize(
            data: $data,
            type: $type,
            format: 'json',
            context: $context
        );
    }

    /**
     * @throws ExceptionInterface
     */
    public function normalize(mixed $data, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        return $this->serializer->normalize(
            data: $data,
            format: 'json',
            context: $context
        );
    }
}
