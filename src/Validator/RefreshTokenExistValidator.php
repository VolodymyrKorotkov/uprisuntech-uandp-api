<?php
namespace App\Validator;

use App\Repository\RefreshTokenRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class RefreshTokenExistValidator extends ConstraintValidator
{
	private RefreshTokenRepository $refreshTokenRepository;

	public function __construct(
		#[Autowire(service: 'App\Repository\RefreshTokenRepository')]
		RefreshTokenRepository $refreshTokenRepository
	)
	{
		$this->refreshTokenRepository = $refreshTokenRepository;
	}

	public function validate($value, Constraint $constraint)
	{
		if (!$constraint instanceof RefreshTokenExist) {
			throw new UnexpectedTypeException($constraint, RefreshTokenExist::class);
		}
		if(!$value){
			return;
		}

		$refreshToken = $this->refreshTokenRepository->findByToken($value);

		if (!$refreshToken) {
			$this->context->buildViolation($constraint->message)
				->setParameter('{{ string }}', $value)
				->addViolation();
		}
	}
}
