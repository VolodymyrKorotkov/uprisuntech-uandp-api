<?php

namespace App\Controller\ApiPlatform\UpdatePassword\Dto;

use App\Validator\KeycloakUserPassword\KeycloakUserPassword;
use App\Validator\Password\PasswordConstraint;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

class UpdatePassword
{
    #[NotBlank]
    #[Groups('safe')]
    #[KeycloakUserPassword]
    public string $oldPassword;

    #[NotBlank]
    #[PasswordConstraint]
    #[Groups('safe')]
    #[Assert\NotEqualTo(
        propertyPath: 'oldPassword',
        message: 'New password is the same as old.'
    )]
    public string $newPassword;
}