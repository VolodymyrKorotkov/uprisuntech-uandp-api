<?php
namespace App\Validator\KeycloakUserPassword;

use App\Service\KeycloakClient\Dto\ValidatePasswordDto;
use App\Service\KeycloakClient\KeycloakClientInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class KeycloakUserPasswordValidator extends ConstraintValidator
{
    const OLD_PASSWORD = 'The old password is incorrect.';

	private KeycloakClientInterface $keycloakClient;
    private Security $security;

	public function __construct(
		#[Autowire(service: 'App\Service\KeycloakClient\KeycloakClient')]
		KeycloakClientInterface $keycloakClient,
        Security $security
	)
	{
		$this->keycloakClient = $keycloakClient;
        $this->security = $security;
	}

	public function validate($value, Constraint $constraint)
	{
		if (!$constraint instanceof KeycloakUserPassword) {
			throw new UnexpectedTypeException($constraint, KeycloakUserPassword::class);
		}
        $dto = new ValidatePasswordDto();
        $dto->password = $value;
        $dto->uuid = $this->security->getUser()->getUserIdentifier();
        if(!$this->keycloakClient->validatePassword($dto)){
            $this->context->buildViolation(self::OLD_PASSWORD)->addViolation();
        }
	}
}
