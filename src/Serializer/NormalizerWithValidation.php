<?php declare(strict_types=1);

namespace App\Serializer;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class NormalizerWithValidation
{
    public function __construct(
        private AppJsonNormalizerCopyInterface $normalizer,
        private ValidatorInterface             $validator
    )
    {
    }

    public function normalize(mixed $data, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $result = $this->normalizer->normalize($data, $context);
        $constraints = $this->validator->validate($result);
        if ($constraints->count()){
            throw new BadRequestException($this->getValidationFailedMessage($constraints));
        }

        return $result;
    }

    private function getValidationFailedMessage(ConstraintViolationListInterface $constraints): string
    {
        $errorMessages = [];
        foreach ($constraints as $constraint){
            $errorMessages[] = $constraint->getPropertyPath().': '.$constraint->getMessage();
        }

        return 'Validation failed. '.implode('. ', $errorMessages);
    }
}
