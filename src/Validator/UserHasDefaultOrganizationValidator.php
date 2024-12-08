<?php declare(strict_types=1);

namespace App\Validator;

use App\Repository\OrganizationRepository;
use App\Repository\UserRepository;
use App\Validator\UserHasDefaaultOrganization\UserHasDefaultOrganization;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class UserHasDefaultOrganizationValidator extends ConstraintValidator
{

    public function __construct(
        private readonly OrganizationRepository $organizationRepository,
        private readonly UserRepository $applicationUserRepository,
    )
    {
    }

    /**
     * @throws NonUniqueResultException
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UserHasDefaultOrganization) {
            throw new UnexpectedTypeException($constraint, UserHasDefaultOrganization::class);
        }

        try {
            $user = $this->applicationUserRepository->findByDrfoCode($value);
            if ($this->organizationRepository->hasUserDefaultOrganization($user->getUserIdentifier())) {
                $this->context
                    ->buildViolation('User has default organization.')
                    ->addViolation();
            }
        } catch (NoResultException){
            return;
        }
    }
}
