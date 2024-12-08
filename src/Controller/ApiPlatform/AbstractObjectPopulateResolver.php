<?php declare(strict_types=1);

namespace App\Controller\ApiPlatform;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

abstract class AbstractObjectPopulateResolver implements SerializerContextBuilderInterface
{
    public const CONTEXT_FIELD = 'object_to_populate_resolver';

    public function __construct(
        private readonly SerializerContextBuilderInterface $decorated
    )
    {
    }

    public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);

        if (!$this->support($context)) {
            return $context;
        }

        $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $this->getObject($context);

        return $context;
    }

    private function support(array $context): bool
    {
        return ($context[self::CONTEXT_FIELD] ?? null) === static::class;
    }

    protected abstract function getObject(): mixed;
}
