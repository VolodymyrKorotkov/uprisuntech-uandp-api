<?php

namespace App\Serializer;

interface AppJsonNormalizerInterface
{
    public function denormalize(mixed $data, string $type, array $context = []): mixed;
    public function normalize(mixed $data, string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null;
}