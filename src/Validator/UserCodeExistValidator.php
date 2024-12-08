<?php
namespace App\Validator;

use App\Repository\UserRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UserCodeExistValidator extends ConstraintValidator
{
	private $userRepository;

	public function __construct(
		#[Autowire(service: 'App\Repository\UserRepository')]
		UserRepository $userRepository
	)
	{
		$this->userRepository = $userRepository;
	}

	public function validate($value, Constraint $constraint)
	{
		if (!$constraint instanceof UserCodeExist) {
			throw new UnexpectedTypeException($constraint, UserCodeExist::class);
		}

		if(!$value){
			return;
		}

		$user = $this->userRepository->findByCode($value);

		if (!$user) {
			$this->context->buildViolation($constraint->message)
				->setParameter('{{ string }}', $value)
				->addViolation();
		}
	}
}
