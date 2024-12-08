<?php declare(strict_types=1);

namespace App\ArgumentResolver;

use App\Serializer\AppJsonNormalizerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class DtoFromRequestArgumentResolver implements ArgumentValueResolverInterface
{
    private AppJsonNormalizerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(AppJsonNormalizerInterface $serializer, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $dto = $this->serializer->denormalize(
            array_merge($request->request->all(), $request->query->all(), $request->attributes->all('_route_params')),
            $argument->getType()
        );

        $constraints = $this->validator->validate($dto);
        if ($constraints->count()){
            throw new ValidationFailedException($dto, $constraints);
        }

        return [$dto];
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
       return $argument->getType() === FromRequestDtoInterface::class ||
           is_subclass_of($argument->getType(), FromRequestDtoInterface::class);
    }
}
