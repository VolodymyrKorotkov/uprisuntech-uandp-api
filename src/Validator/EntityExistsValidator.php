<?php declare(strict_types=1);

namespace App\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class EntityExistsValidator extends ConstraintValidator
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof EntityExists) {
            throw new UnexpectedTypeException($constraint, EntityExists::class);
        }

        if (null === $value){
            return;
        }

        if (!$this->em->contains($value)){
            $this->context
                ->buildViolation($this->getMessage($value, $constraint))
                ->addViolation();
        }
    }

    /**
     * @param EntityExists $constraint
     * @return string|null
     */
    public function getMessage(object $entity, EntityExists $constraint): ?string
    {
        if ($constraint->message){
            return $constraint->message;
        }

        return 'Entity not found: ' . (new \ReflectionClass($entity))->getShortName();
    }
}
