<?php declare(strict_types=1);

namespace App\Controller\ApiPlatform\ResetPassword\Dto;

use App\Validator\Password\PasswordConstraint;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\IdenticalTo;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ResetPasswordDto
{
    #[Groups('safe')]
    #[NotBlank]
    #[PasswordConstraint]
    public string|null $password = null;

    #[IdenticalTo(propertyPath: 'password')]
    #[NotBlank]
    #[Groups('safe')]
    public string|null $confirmPassword = null;

    #[Groups('safe')]
    #[NotBlank]
    public string|null $resetPasswordHash = null;
}
