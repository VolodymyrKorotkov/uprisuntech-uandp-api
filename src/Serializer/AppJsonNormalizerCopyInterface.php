<?php

namespace App\Serializer;

interface AppJsonNormalizerCopyInterface
{
    public function denormalize(mixed $data, string $type, array $context = []): mixed;
    public function normalize(mixed $data, array $context = []): array|string|int|float|bool|\ArrayObject|null;
}
