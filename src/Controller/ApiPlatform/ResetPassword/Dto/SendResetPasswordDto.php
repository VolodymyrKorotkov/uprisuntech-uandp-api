<?php declare(strict_types=1);

namespace App\Controller\ApiPlatform\ResetPassword\Dto;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

final class SendResetPasswordDto
{
    #[Groups('safe')]
    #[Email]
    #[NotBlank]
    public ?string $email = null;

    #[Groups('safe')]
    #[NotBlank]
    public ?string $siteAlias = null;
}
