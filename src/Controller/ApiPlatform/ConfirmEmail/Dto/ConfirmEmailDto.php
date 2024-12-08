<?php

namespace App\Controller\ApiPlatform\ConfirmEmail\Dto;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;

class ConfirmEmailDto
{
    #[Groups('safe')]
    #[NotBlank]
    public string|null $confirmEmailHash = null;

}