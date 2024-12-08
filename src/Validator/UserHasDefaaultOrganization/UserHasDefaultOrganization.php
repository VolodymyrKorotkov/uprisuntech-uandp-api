<?php declare(strict_types=1);

namespace App\Validator\UserHasDefaaultOrganization;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class UserHasDefaultOrganization extends Constraint
{
    public string|null $message = null;
}