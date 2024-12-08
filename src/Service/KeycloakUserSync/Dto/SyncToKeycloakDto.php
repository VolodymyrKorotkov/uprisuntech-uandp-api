<?php declare(strict_types=1);

namespace App\Service\KeycloakUserSync\Dto;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class SyncToKeycloakDto
{
    #[Groups('safe')]
    #[Assert\NotBlank(message: "User identity is required.")]
    public ?string $identity = null;

}
