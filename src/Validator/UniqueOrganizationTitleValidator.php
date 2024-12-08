<?php declare(strict_types=1);

namespace App\Validator;

use App\Repository\OrganizationRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class UniqueOrganizationTitleValidator extends ConstraintValidator
{
    public function __construct(
        private readonly OrganizationRepository $organizationRepository
    )
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueOrganizationTitle) {
            throw new UnexpectedTypeException($constraint, UniqueOrganizationTitle::class);
        }

        if (empty($value)) {
            return;
        }

        if ($this->organizationRepository->hasByTitle($value)) {
            $this->context
                ->buildViolation('Organization with this title already exists')
                ->addViolation();
        }
    }
}
