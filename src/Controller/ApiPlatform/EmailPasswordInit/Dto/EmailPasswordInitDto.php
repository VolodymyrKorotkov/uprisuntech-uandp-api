<?php declare(strict_types=1);

namespace App\Controller\ApiPlatform\EmailPasswordInit\Dto;

use App\Validator\Email\UniqueEmailConstraint;
use App\Validator\Password\PasswordConstraint;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class EmailPasswordInitDto
{
    #[Groups('safe')]
    #[Assert\NotBlank(message: "Email is required.")]
    #[Assert\Email(
        message: "The email '{{ value }}' is not a valid email."
    )]
    #[UniqueEmailConstraint]
    public ?string $email = null;

    #[Groups('safe')]
    #[Assert\NotBlank(message: "Password is required.")]
    #[PasswordConstraint]
    public ?string $password = null;
}


