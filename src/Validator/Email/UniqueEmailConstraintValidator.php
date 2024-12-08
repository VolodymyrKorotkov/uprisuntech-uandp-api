<?php
namespace App\Validator\Email;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueEmailConstraintValidator extends ConstraintValidator
{
    private const EMAIL_ALREADY_IN_USE = 'This email is already in use.';

    public function __construct(
        private UserRepository $userRepository,
        private Security $security
    ){}

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueEmailConstraint) {
            throw new UnexpectedTypeException($constraint, UniqueEmailConstraint::class);
        }

        if ('' === $value || !is_string($value)) {
            return;
        }

        $this->validateEmail($value);
    }

    private function validateEmail($value): void
    {
        $currentUser = $this->security->getUser();

        $userByEmail = $this->getUserByEmail($value);

        if(!$userByEmail){
            return;
        }

        if ($currentUser->getUserIdentifier() !== $userByEmail->getUserIdentifier()) {
            $this->context->buildViolation(self::EMAIL_ALREADY_IN_USE)->addViolation();
        }
    }

    private function getUserByEmail(string $username): ?User
    {
        return $this->userRepository->findOneBy(['email' => $username]);
    }
}