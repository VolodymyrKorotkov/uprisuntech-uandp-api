<?php declare(strict_types=1);

namespace App\Serializer;

use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

final class SnakeCaseToCamelCaseNameConverter implements NameConverterInterface
{
    public function __construct(
        private readonly ?array $attributes = null,
        private readonly bool   $lowerCamelCase = true,
    ) {
    }

    public function normalize(string $propertyName): string
    {
        return $propertyName;
    }

    public function denormalize(string $propertyName): string
    {
        $camelCasedName = preg_replace_callback('/(^|_|\.)+(.)/', fn ($match) => ('.' === $match[1] ? '_' : '').strtoupper($match[2]), $propertyName);

        if ($this->lowerCamelCase) {
            $camelCasedName = lcfirst($camelCasedName);
        }

        if (null === $this->attributes || \in_array($camelCasedName, $this->attributes)) {
            return $camelCasedName;
        }

        return $propertyName;
    }
}
