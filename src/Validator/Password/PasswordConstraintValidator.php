<?php
namespace App\Validator\Password;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PasswordConstraintValidator extends ConstraintValidator
{
    private const TOO_SHORT_MESSAGE = 'The password must be at least 8 characters long.';
    private const TOO_LONG_MESSAGE = 'The password must be at most 32 characters long.';
    private const MISSING_NUMBER_MESSAGE = 'The password must contain at least one number.';
    private const MISSING_LETTER_MESSAGE = 'The password must contain at least one letter.';
    private const MISSING_UPPER_CASE_LETTER_MESSAGE = 'The password must contain at least one uppercase letter.';
    private const MISSING_LOWER_CASE_LETTER_MESSAGE = 'The password must contain at least one lowercase letter.';
    private const MISSING_SPECIAL_CHARACTER_MESSAGE = 'The password must contain at least one special character: !#$%&()*+,-./:;<=>?@[\]^_{|}~';
    private const INVALID_CHARACTERS_MESSAGE = 'The password must only contain characters from the Latin alphabet, numbers, and the allowed special character: !#$%&()*+,-./:;<=>?@[\]^_{|}~';

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof PasswordConstraint) {
            throw new UnexpectedTypeException($constraint, PasswordConstraint::class);
        }

        if ('' === $value || !is_string($value)) {
            return;
        }

        $this->validateLength($value);
        $this->validateContainsNumber($value);
        $this->validateContainsLetter($value);
        $this->validateContainsUpperCaseLetter($value);
        $this->validateContainsLowerCaseLetter($value);
        $this->validateContainsSpecialCharacter($value);
        $this->validateAllowedCharacters($value);
    }

    private function validateLength(string $value): void
    {
        if (strlen($value) < 8) {
            $this->context->buildViolation(self::TOO_SHORT_MESSAGE)->addViolation();
        } elseif (strlen($value) > 32) {
            $this->context->buildViolation(self::TOO_LONG_MESSAGE)->addViolation();
        }
    }

    private function validateContainsNumber(string $value): void
    {
        if (!preg_match('/[0-9]/', $value)) {
            $this->context->buildViolation(self::MISSING_NUMBER_MESSAGE)->addViolation();
        }
    }

    private function validateContainsLetter(string $value): void
    {
        if (!preg_match('/[a-zA-Z]/', $value)) {
            $this->context->buildViolation(self::MISSING_LETTER_MESSAGE)->addViolation();
        }
    }

    private function validateContainsUpperCaseLetter(string $value): void
    {
        if (!preg_match('/[A-Z]/', $value)) {
            $this->context->buildViolation(self::MISSING_UPPER_CASE_LETTER_MESSAGE)->addViolation();
        }
    }

    private function validateContainsSpecialCharacter(string $value): void
    {
        if (!preg_match('/[!#$%&()*+,-.\/:;<=>?@\[\]^_{|}~]/', $value)) {
            $this->context->buildViolation(self::MISSING_SPECIAL_CHARACTER_MESSAGE)->addViolation();
        }
    }

    private function validateContainsLowerCaseLetter(string $value): void
    {
        if (!preg_match('/[a-z]/', $value)) {
            $this->context->buildViolation(self::MISSING_LOWER_CASE_LETTER_MESSAGE)->addViolation();
        }
    }

    private function validateAllowedCharacters(string $value): void
    {
        if (!preg_match('/^[a-zA-Z0-9!#$%&()*+,-.\/:;<=>?@\[\]^_{|}~]+$/', $value)) {
            $this->context->buildViolation(self::INVALID_CHARACTERS_MESSAGE)->addViolation();
        }
    }
}