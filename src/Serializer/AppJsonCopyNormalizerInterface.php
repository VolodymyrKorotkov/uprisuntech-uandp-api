<?php

namespace App\Serializer;

interface AppJsonCopyNormalizerInterface
{
    public function denormalize(mixed $data, string $type, array $context = []): mixed;
    public function normalize(mixed $data, array $context = []): array|string|int|float|bool|\ArrayObject|null;
}