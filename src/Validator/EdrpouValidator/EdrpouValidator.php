<?php declare(strict_types=1);

namespace App\Validator\EdrpouValidator;

use App\Entity\OrganizationJoinFlowData;
use App\Repository\OrganizationRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class EdrpouValidator extends ConstraintValidator
{
    private const EDRPOU_EXISTS_ERROR = 'Edrpou is exists in another organization';
    private OrganizationRepository $organizationRepository;

    public function __construct(
        OrganizationRepository $organizationRepository
    )
    {
        $this->organizationRepository = $organizationRepository;
    }

    /**
     * @param OrganizationJoinFlowData $value
     * @param Constraint $constraint
     * @return void
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Edrpou) {
            throw new UnexpectedTypeException($constraint, Edrpou::class);
        }

        if (null === $value) {
            return;
        }

        if ($this->organizationRepository->isExistsEdrpou($value->getEdrpou())) {
            $this->context->buildViolation(self::EDRPOU_EXISTS_ERROR)
                ->atPath('edrpou')
                ->addViolation();
        }
    }

    /**
     * @param object $entity
     * @param Edrpou $constraint
     * @return string|null
     */
    public function getMessage(object $entity, Edrpou $constraint): ?string
    {
        if ($constraint->message){
            return $constraint->message;
        }

        return 'Edrpo is exists: ' . (new \ReflectionClass($entity))->getShortName();
    }
}
